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
                    'color' => $this->colorType($type),
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

        Http::post(config('env.slackWebhookUrl'), $message);
    }

    public function line(string $type, Reservation $reservation)
    {
        $dateDiff = today()
            ->diffInDays($reservation->start
                ->setHour(0)
                ->setMinute(0)
                ->setSecond(0), false);

        $replace = [
            "%altText%" => "「{$reservation->name}」が{$this->message($type)}",
            "%color%" => $this->color($type),
            "%headerMessage%" => $this->message($type),
            "%name%" => $reservation->name,
            "%date%" => $reservation->start->isoFormat('Y年M月D日 (ddd)'),
            "%startTime%" => $reservation->start->format('G:i'),
            "%endTime%" => $reservation->end->format('G:i'),
            "%buttonLink%" => config('env.clientApplicationUrl') . "?p={$dateDiff}",
        ];

        $linePayloadJson = strtr(json_encode(config('line')), $replace);

        $accessToken = config('env.lineAccessToken');

        $headers = ["Authorization" => "Bearer {$accessToken}"];

        $url = "https://api.line.me/v2/bot/message/narrowcast";
        Http::withHeaders($headers)->post($url, json_decode($linePayloadJson, true));
    }

    private function color(string $type): string
    {
        return match ($type) {
            'create' => '#1A237E',
            'update' => '#F57D17',
            'delete' => '#B71C1C',
        };
    }

    private function colorType(string $type): string
    {
        return match ($type) {
            'create' => 'good',
            'update' => 'warning',
            'delete' => 'danger',
        };
    }

    private function message(string $type): string
    {
        return match ($type) {
            'create' => '予約されました',
            'update' => '変更されました',
            'delete' => '削除されました',
        };
    }
}
