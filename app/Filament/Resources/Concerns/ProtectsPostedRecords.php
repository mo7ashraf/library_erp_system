<?php

namespace App\Filament\Resources\Concerns;

use Illuminate\Database\Eloquent\Model;

trait ProtectsPostedRecords
{
    public static function canEdit(Model $record): bool
    {
        return ! static::isPostedRecord($record);
    }

    public static function canDelete(Model $record): bool
    {
        return ! static::isPostedRecord($record);
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    protected static function isPostedRecord(Model $record): bool
    {
        return isset($record->status) && $record->status === 'posted';
    }
}