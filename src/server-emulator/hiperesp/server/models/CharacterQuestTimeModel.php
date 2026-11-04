<?php declare(strict_types=1);
namespace hiperesp\server\models;

use hiperesp\server\vo\CharacterVO;
use hiperesp\server\vo\QuestVO;

/**
 * Stores last quest completion times. Quest minTime values come from migrated quest data,
 * so client updates that change timers keep working after a normal data upgrade.
 */
class CharacterQuestTimeModel extends Model {

    public const COLLECTION = 'char_quest_time';

    public function getLastCompletedAt(CharacterVO $char, QuestVO $quest): ?string {
        $row = $this->storage->select(self::COLLECTION, [
            'charId' => $char->id,
            'questId' => $quest->id,
        ]);
        if(isset($row[0]) && $row = $row[0]) {
            return $row['lastCompletedAt'] ?? null;
        }
        return null;
    }

    public function markCompleted(CharacterVO $char, QuestVO $quest): void {
        $existing = $this->storage->select(self::COLLECTION, [
            'charId' => $char->id,
            'questId' => $quest->id,
        ]);
        $now = \date('c');
        if(isset($existing[0]) && $existing = $existing[0]) {
            $this->storage->update(self::COLLECTION, [
                'id' => $existing['id'],
                'lastCompletedAt' => $now,
            ]);
            return;
        }
        $this->storage->insert(self::COLLECTION, [
            'charId' => $char->id,
            'questId' => $quest->id,
            'lastCompletedAt' => $now,
        ]);
    }

}
