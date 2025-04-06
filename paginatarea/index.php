<?php

$token = '8024202425:AAHZU-uiizGkjfUweisefXFMx4QSd7G-ENs';
$api_url = "https://api.telegram.org/bot$token/";

// Diccionario de pasillos
$pasillos = [
    "pasillo 1" => ["Carne", "Queso", "Jamón"],
    "pasillo 2" => ["Leche", "Yogurth", "Cereal"],
    "pasillo 3" => ["Bebidas", "Jugos"],
    "pasillo 4" => ["Pan", "Pasteles", "Tortas"],
    "pasillo 5" => ["Detergente", "Lavaloza"]
];

// Obtener actualizaciones usando getUpdates (long polling)
$response = file_get_contents($api_url . "getUpdates?offset=-1"); // Solo obtiene los nuevos mensajes
$updates = json_decode($response, true);

// Mostrar respuesta completa para depuración
echo "<h1>Depuración: Mensajes Recibidos</h1>";
echo "<pre>";
print_r($updates);
echo "</pre>";

// Si no hay actualizaciones, mostrar un mensaje
if (empty($updates['result'])) {
    echo "<p>No se encontraron mensajes nuevos.</p>";
} else {
    // Procesar actualizaciones
    foreach ($updates['result'] as $update) {
        if (isset($update['message'])) {
            $message = $update['message'];
            $chat_id = $message['chat']['id'];
            $text = strtolower(trim($message['text'] ?? ""));

            echo "<p><strong>Mensaje recibido:</strong> $text</p>";

            // Crear el teclado con el botón de "Hola"
            $keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => 'Enviar Hola', 'callback_data' => 'hola']
                    ]
                ]
            ];
            $encodedKeyboard = json_encode($keyboard);

            // Si el texto contiene un pasillo, responder con los productos del pasillo
            $respuesta = "Lo siento, no encontré ese pasillo.";
            foreach ($pasillos as $pasillo => $productos) {
                if (strpos($text, strtolower($pasillo)) !== false) {
                    $respuesta = "En $pasillo puedes encontrar: " . implode(", ", $productos);
                    break;
                }
            }

            // Si el texto es "hola", enviar un mensaje de saludo
            if ($text == "hola") {
                $respuesta = "¡Hola! ¿En qué te puedo ayudar?";
            }

            // Enviar respuesta al usuario con el teclado
            $url = $api_url . "sendMessage?chat_id=$chat_id&text=" . urlencode($respuesta) . "&reply_markup=" . urlencode($encodedKeyboard);
            file_get_contents($url); // Enviar el mensaje con el teclado
            echo "<p><strong>Respuesta enviada:</strong> $respuesta</p>";
        }
    }
}

?>