<?php

class GuideTokenCommand extends CConsoleCommand
{
    const SELECTION_FILE = 'reset_guide_tokens_selected_users.json';

    public function actionResetSaved()
    {
        $selectionFile = Yii::app()->runtimePath . DIRECTORY_SEPARATOR . self::SELECTION_FILE;
        if (!is_file($selectionFile)) {
            echo "No saved user selection found.\n";
            return 1;
        }

        $data = json_decode(@file_get_contents($selectionFile), true);
        $userIds = (is_array($data) && !empty($data['userIds']) && is_array($data['userIds'])) ? $data['userIds'] : array();
        $userIds = $this->normalizeUserIds($userIds);

        if (empty($userIds)) {
            echo "No saved user IDs to process.\n";
            return 1;
        }

        $logFile = Yii::app()->runtimePath . DIRECTORY_SEPARATOR . 'reset_guide_tokens.log';
        $results = array();
        $results[] = 'Guide token refresh started: ' . date('Y-m-d H:i:s');

        foreach ($userIds as $userId) {
            $user = Uzytkownik::model()->findByPk($userId);
            if ($user === null) {
                $results[] = "NOT FOUND: ID {$userId}";
                continue;
            }

            $user->guide_mobile_token = '';
            if ($user->update(array('guide_mobile_token'))) {
                $results[] = "SUCCESS: {$user->login} ({$user->imie} {$user->nazwisko}) - id={$user->id}";
            } else {
                $results[] = "FAILED: {$user->login} ({$user->imie} {$user->nazwisko}) - id={$user->id}";
            }
        }

        $results[] = 'Guide token refresh finished: ' . date('Y-m-d H:i:s');
        file_put_contents($logFile, implode("\n", $results) . "\n", FILE_APPEND);

        foreach ($results as $line) {
            echo $line . "\n";
        }

        return 0;
    }

    protected function normalizeUserIds($userIds)
    {
        $normalized = array();
        foreach ((array) $userIds as $userId) {
            $userId = (int) $userId;
            if ($userId > 0) {
                $normalized[$userId] = $userId;
            }
        }

        return array_values($normalized);
    }
}
