<?php

namespace App\Plugins;

class EmailPlugin
{
    public static $tableEmail = 'snv_emails_email';
    public static $tableEmailSchema = [
        array("Id", "STRING", "NOT NULL PRIMARY KEY"),
        array("Status", "STRING"),
        array("From", "STRING"),
        array("To", "STRING"),
        array("Cc", "STRING"),
        array("Bcc", "STRING"),
        array("Subject", "STRING"),
        array("Html", "TEXT"),
        array("Text", "TEXT"),
        array("Attachment", "TEXT"),
        array("Error", "STRING"),
        array("Description", "TEXT"),
        array("SentAt", "DATETIME"),
        array("CreatedAt", "DATETIME"),
        array("UpdatedAt", "DATETIME"),
        array("DeletedAt", "DATETIME"),
    ];
}