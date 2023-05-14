<?php

namespace App\Utils;

use App\Models\Reservation;
use Illuminate\Support\Facades\Http;

class Notification
{
    public function slack(string $type, Reservation $reservation)
    {
        $message = [
            'text' => $this->message($type),
            'attachments' => [
                [
                    'color' => $this->color($type),
                    'fields' => [
                        [
                            'title' => $reservation->name,
                            'value' => "{$reservation->start->format('Y-m-d H:i')} - {$reservation->end->format('H:i')}",
                        ],
                        [
                            'value' => "パスコード: {$reservation->passcode}",
                        ]
                    ]
                ]
            ]
        ];

        Http::post(config('env.slackWebhookUrl'), json_encode($message));
    }

    public function line(string $type, Reservation $reservation)
    {
        $dateDiff = $reservation->start->diffInDays(now());
        $replace = [
            "%headerMessage%" => $this->message($type),
            "%name%" => $reservation->name,
            "%date%" => $reservation->start->format('Y年m月d日 (D)'),
            "%startTime%" => $reservation->start->format('G:i'),
            "%endTime%" => $reservation->start->format('G:i'),
            "%buttonLink%" => config('env.clientApplicationUrl') . "?p={$dateDiff}",
        ];

        $linePayloadJson = strtr(json_encode(config('line')), $replace);

        $accessToken = config('env.lineAccessToken');

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken
        ];

        $url = "https://api.line.me/v2/bot/message/narrowcast";

        Http::withHeaders($headers)->post($url, $linePayloadJson);
    }

    private function color(string $type): string
    {
        return match ($type) {
            'create' => '#36a64f',
            'update' => '#f2c744',
            'delete' => '#d00000',
        };
    }

    private function message(string $type): string
    {
        return match ($type) {
            'create' => '予約されました。',
            'update' => '変更されました。',
            'delete' => '削除されました。',
        };
    }
}
