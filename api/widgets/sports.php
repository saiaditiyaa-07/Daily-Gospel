<?php
/**
 * Daily Gospel - Sports Widget API Endpoint
 * Serves live cricket score data and headlines
 */

declare(strict_types=1);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Content-Type: application/json; charset=utf-8');

$sportsData = [
    'live_match' => [
        'teams' => 'IND vs AUS (T20 World Cup)',
        'teams_ta' => 'இந்தியா எதிர் ஆஸ்திரேலியா (டி20 உலகக் கோப்பை)',
        'status' => 'In Progress - Innings Break',
        'status_ta' => 'விளையாட்டு நடந்து கொண்டிருக்கிறது - இடைவேளை',
        'score' => 'IND: 196/5 (20.0 Over) | AUS: 0/0 (0.0 Over)',
        'score_ta' => 'IND: 196/5 (20.0 ஓவர்) | AUS: 0/0 (0.0 ஓவர்)'
    ],
    'headlines' => [
        'India posts a massive total of 196 against Australia in Super 8 stage',
        'Hardik Pandya slams quick-fire 45 off 18 balls to lift the score',
        'CSK resumes training camp in Chennai ahead of qualifiers'
    ],
    'headlines_ta' => [
        'சூப்பர் 8 சுற்றில் ஆஸ்திரேலியாவுக்கு எதிராக இந்தியா 196 ரன்கள் குவித்தது',
        'ஹர்திக் பாண்டியா 18 பந்துகளில் 45 ரன்கள் விளாசி ஸ்கோரை உயர்த்தினார்',
        'தகுதிச் சுற்றுக்கு முன்னதாக சென்னையில் சி.எஸ்.கே அணி பயிற்சியை தொடங்கியது'
    ]
];

echo json_encode($sportsData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
exit;
