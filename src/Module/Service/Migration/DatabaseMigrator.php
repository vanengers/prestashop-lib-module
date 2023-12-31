<?php

namespace Vanengers\PrestashopLibModule\Module\Service\Migration;

use Db;
use Module;
use PrestaShopDatabaseException;
use Throwable;

/**
 * Place the migration files inside root/sql folder of your module.
 * Format: YYYYMMDDHHMM - Type - TableName.sql
 * Example: 202308301730 - I - ps_orders.sql
 * Type: I - Insert, U - Update, T - Truncate, D - Delete, A - Alter, C - Create (abbreviation of SQL command)
 * ALLWAYS USE PREFIX FOR TABLES: _DB_PREFIX_ (this will be parsed into the PS prefix)
 * ALTER TABLE `_DB_PREFIX_image` ......  results in ALTER TABLE `ps_image` ......
 */
class DatabaseMigrator
{
    /**
     * @var Module
     */
    private Module $module;

    /**
     * @param Module $module
     * @return bool
     */
    public function init(Module $module) : bool
    {
        $this->module = $module;

        return true;
    }

    /**
     * @return bool
     * @author George van Engers <george@dewebsmid.nl>
     * @since 26-09-2023
     */
    public function migrateUp() : bool
    {
        $current_migration = $this->currentlyAtMigration();
        $migrationsToExecute = $this->getMigrationsFrom($current_migration);

        if (is_null($current_migration)) {
            $this->createMigrationTable();
        }

        foreach($migrationsToExecute as $execute) {
            if (!$this->migrateTo($execute)) {
                // This should stop migration further, and stops from logging it to the migration table.
                return false;
            }
        }

        return true;
    }

    /**
     * @return bool
     * @author George van Engers <george@dewebsmid.nl>
     * @since 26-09-2023
     */
    public function migrateDown()
    {
        $getUninstallSqlFiles = glob($this->module->getLocalPath().'sql/uninstall/*.sql');
        foreach($getUninstallSqlFiles as $file) {
            try {
                $contents = file_get_contents($file);
                Db::getInstance()->execute($contents);
            } catch (Throwable $e) {
                return false;
            }
        }

        $this->dropMigrationTable();

        return true;
    }

    /**
     * @return mixed|null
     * @throws PrestaShopDatabaseException
     * @since 26-09-2023
     * @author George van Engers <george@dewebsmid.nl>
     */
    private function currentlyAtMigration(): int|null
    {
        if ($this->tableExists(_DB_PREFIX_.$this->module->name.'_migration')) {
            $result = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.$this->module->name.'_migration ORDER BY id DESC LIMIT 1');
            if (count($result) > 0) {
                return (int) $this->getTimestampFromFileName($result[0]['filename']);
            }
        }

        return null;
    }

    /**
     * @param $fileName
     * @return int
     * @author George van Engers <george@dewebsmid.nl>
     * @since 04-10-2023
     */
    private function getTimestampFromFileName($fileName)
    {
        $split = explode('-', $fileName);
        $fNameExplode = explode('/',$split[0]);
        $format = (int) trim($fNameExplode[count($fNameExplode)-1]);
        return $format;
    }

    /**
     * @todo Match only files with pattern. Use regex here.
     * @param $current_migration
     * @return array
     * @author George van Engers <george@dewebsmid.nl>
     * @since 26-09-2023
     */
    private function getMigrationsFrom($current_migration): array
    {
        $installSqlFiles = glob($this->module->getLocalPath().'sql/install/*.sql');
        return $this->orderFilesByTime($this->module->getLocalPath().'sql/install/', $installSqlFiles, $current_migration);
    }

    /**
     * @param mixed $execute
     * @return bool
     * @author George van Engers <george@dewebsmid.nl>
     * @since 26-09-2023
     */
    private function migrateTo(mixed $execute): bool
    {
        $contents = file_get_contents($execute);

        try {
            Db::getInstance()->execute($this->parseSql($contents));
            $this->addMigrationRecord($execute);
        } catch (Throwable $e) {
            return false;
        }

        return true;
    }

    /**
     * @param $table
     * @return bool
     * @author George van Engers <george@dewebsmid.nl>
     * @since 26-09-2023
     */
    private function tableExists($table): bool
    {
        try {
            $result = Db::getInstance()->executeS("SHOW TABLES LIKE '".$table."'");
        } catch (Throwable $e) {
            return FALSE;
        }

        return count($result) > 0;
    }

    /**
     * @param $folder
     * @param $installSqlFiles
     * @param $current_migration
     * @return array
     * @author George van Engers <george@dewebsmid.nl>
     * @since 26-09-2023
     */
    private function orderFilesByTime($folder, $installSqlFiles, $current_migration) : array
    {
        $returnable = [];

        foreach($installSqlFiles as $file) {
            /* Format: YYYYMMDDHHMM - Type - TableName.sql */
            $format = $this->getTimestampFromFileName($file);
            if ($format > $current_migration) {
                $returnable[$format] = $file;
            }
        }

        ksort($returnable);
        return $returnable;
    }

    /**
     * @return void
     * @author George van Engers <george@dewebsmid.nl>
     * @since 26-09-2023
     */
    private function createMigrationTable(): void
    {
        $sql = 'CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.$this->module->name.'_migration (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            filename VARCHAR(255) NOT NULL,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )';

        Db::getInstance()->execute($sql);
    }

    /**
     * @param $filename
     * @return void
     * @author George van Engers <george@dewebsmid.nl>
     * @since 26-09-2023
     */
    private function addMigrationRecord($filename): void
    {
        $sql = 'INSERT INTO '._DB_PREFIX_.$this->module->name.'_migration (filename) VALUES ("'.$filename.'")';
        Db::getInstance()->execute($sql);
    }

    /**
     * @return void
     * @author George van Engers <george@dewebsmid.nl>
     * @since 26-09-2023
     */
    public function dropMigrationTable(): void
    {
        Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.$this->module->name.'_migration');
    }

    /**
     * @param string $contents
     * @return string
     * @author George van Engers <george@dewebsmid.nl>
     * @since 27-09-2023
     */
    private function parseSql(string $contents)
    {
        return str_replace('_DB_PREFIX_',_DB_PREFIX_,$contents);
    }
}