<?php

return [
    "type" => "bubble",
    "size" => "mega",
    "header" => [
        "type" => "box",
        "layout" => "vertical",
        "contents" => [
            [
                "type" => "text",
                "text" => "%headerMessage%",
                "color" => "#ffffff",
                "size" => "md",
                "flex" => 4,
                "weight" => "bold"
            ]
        ],
        "paddingAll" => "20px",
        "backgroundColor" => "%color%",
        "spacing" => "md",
        "paddingTop" => "22px"
    ],
    "body" => [
        "type" => "box",
        "layout" => "vertical",
        "contents" => [
            [
                "type" => "text",
                "text" => "%name%",
                "weight" => "bold",
                "size" => "lg",
                "margin" => "none",
                "color" => "#111111"
            ],
            [
                "type" => "text",
                "text" => "%date%",
                "margin" => "lg",
                "size" => "md",
                "color" => "#444444"
            ],
            [
                "type" => "text",
                "text" => "%startTime% - %endTime%",
                "size" => "xxl",
                "color" => "#444444",
                "style" => "normal",
                "decoration" => "none",
                "align" => "start",
                "margin" => "none"
            ]
        ],
        "backgroundColor" => "#ffffff"
    ],
    "footer" => [
        "type" => "box",
        "layout" => "vertical",
        "contents" => [
            [
                "type" => "button",
                "action" => [
                    "type" => "uri",
                    "label" => "確認する",
                    "uri" => "%buttonLink%"
                ],
                "style" => "secondary",
                "gravity" => "top"
            ]
        ],
        "margin" => "sm"
    ],
    "styles" => [
        "footer" => [
            "separator" => false
        ]
    ]
];
