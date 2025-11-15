<?php
require_once '../config/database.php';

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $user_transcript = isset($input['transcript']) ? $input['transcript'] : '';
    $mock_test_id = isset($input['mock_test_id']) ? $input['mock_test_id'] : null;

    // --- Gemini API Key ---
    // Get the API key from environment variable.
    // Replace 'YOUR_GEMINI_API_KEY' with your actual key if not using .env or getenv().
    $gemini_api_key = "AIzaSyB7QddNHp4zWRjKT-ZUQduiWrcpDD3MqgE";
    if (!$gemini_api_key) {
        // Fallback if getenv() doesn't work (e.g., if .env not loaded or var not set)
        // In a production environment, you should ensure this is securely configured.
        $response = ['status' => 'error', 'message' => 'Gemini API Key not configured.'];
        echo json_encode($response);
        exit;
    }

    if (!$user_id) {
        $response = ['status' => 'error', 'message' => 'User not authenticated.'];
    } elseif (!$mock_test_id) {
        $response = ['status' => 'error', 'message' => 'Mock test ID not provided.'];
    } else {
        try {
            // Fetch the AI prompt from the database
            $stmt = $pdo->prepare("SELECT ai_prompt FROM interview_mock_tests WHERE id = ? AND status = 1");
            $stmt->execute([$mock_test_id]);
            $mock_test = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$mock_test) {
                $response = ['status' => 'error', 'message' => 'Mock test not found or inactive.'];
            } else {
                $ai_initial_prompt = $mock_test['ai_prompt'];
                $ai_reply = '';

                // Check if this is an initial load (empty transcript) and clear history if so
                if (empty($user_transcript)) {
                    // Clear previous conversation history for this user and mock test
                    $stmt_clear_history = $pdo->prepare("DELETE FROM interview_conversations WHERE interview_mock_test_id = ? AND user_id = ?");
                    $stmt_clear_history->execute([$mock_test_id, $user_id]);
                }

                // Fetch conversation history from the database (after potential clearing)
                $history_stmt = $pdo->prepare("SELECT sender, message FROM interview_conversations WHERE interview_mock_test_id = ? AND user_id = ? ORDER BY created_at ASC");
                $history_stmt->execute([$mock_test_id, $user_id]);
                $conversation_history = $history_stmt->fetchAll(PDO::FETCH_ASSOC);

                // Prepare chat history for Gemini API
                $gemini_chat_contents = [];
                foreach ($conversation_history as $chat_message) {
                    $role = ($chat_message['sender'] === 'user') ? 'user' : 'model';
                    $gemini_chat_contents[] = [
                        'role' => $role,
                        'parts' => [['text' => $chat_message['message']]]
                    ];
                }
                
                $finalprompt = $ai_initial_prompt . "Candidate answer" . $user_transcript;   

                // If this is the very first interaction for this mock test and user (after potential clear),
                // the initial prompt from the database should be the first AI message.
                if (empty($user_transcript) && empty($conversation_history)) {
                    $ai_reply = $ai_initial_prompt;
                    // Log initial AI message
                    $stmt_insert_ai = $pdo->prepare("INSERT INTO interview_conversations (interview_mock_test_id, user_id, sender, message) VALUES (?, ?, 'ai', ?)");
                    $stmt_insert_ai->execute([$mock_test_id, $user_id, $ai_reply]);
                } else {
                    // Log user message if provided
                    if (!empty($user_transcript)) {
                        $stmt_insert_user = $pdo->prepare("INSERT INTO interview_conversations (interview_mock_test_id, user_id, sender, message) VALUES (?, ?, 'user', ?)");
                        $stmt_insert_user->execute([$mock_test_id, $user_id, $user_transcript]);
                        // Add user's current message to the history for Gemini
                        $gemini_chat_contents[] = [
                            'role' => 'user',
                            'parts' => [['text' =>  $finalprompt]]
                        ];
                    }

                    // --- Gemini API Call using cURL ---
                    $api_url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $gemini_api_key;

                    $payload = [
                        'contents' => $gemini_chat_contents,
                        // Optional: Add generation config for more control (e.g., temperature, max output tokens)
                        // 'generationConfig' => [
                        //     'temperature' => 0.7,
                        //     'maxOutputTokens' => 500,
                        // ],
                        // Optional: Add safety settings if needed
                        // 'safetySettings' => [
                        //     ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                        //     ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                        //     ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                        //     ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                        // ],
                    ];

                    $ch = curl_init($api_url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        'Content-Type: application/json',
                        'Accept: application/json'
                    ]);

                    $api_response = curl_exec($ch);
                    
                    // Check for cURL errors
                    if (curl_errno($ch)) {
                        throw new Exception("cURL Error: " . curl_error($ch));
                    }
                    
                    curl_close($ch);

                    $decoded_response = json_decode($api_response, true);

                    // Check if Gemini returned a valid response
                    if (isset($decoded_response['candidates'][0]['content']['parts'][0]['text'])) {
                        $ai_reply = $decoded_response['candidates'][0]['content']['parts'][0]['text'];
                    } elseif (isset($decoded_response['error'])) {
                        throw new Exception("Gemini API Error: " . $decoded_response['error']['message']);
                    } else {
                        throw new Exception("Unexpected Gemini API response structure: " . $api_response);
                    }

                    // Log AI message
                    $stmt_insert_ai = $pdo->prepare("INSERT INTO interview_conversations (interview_mock_test_id, user_id, sender, message) VALUES (?, ?, 'ai', ?)");
                    $stmt_insert_ai->execute([$mock_test_id, $user_id, $ai_reply]);
                }

                $response = ['status' => 'success', 'ai_response' => $ai_reply];
            }
        } catch (Exception $e) {
            // Log the error for debugging
            error_log("Gemini API Integration Error: " . $e->getMessage());
            $response = ['status' => 'error', 'message' => 'Failed to get AI response: ' . $e->getMessage()];
        }
    }
}

echo json_encode($response);
?>
