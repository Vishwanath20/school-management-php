<?php
session_start();
if(isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: dashboard/dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - GEO IAS</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Toastr CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
    <!-- Remove face-api.js script -->
    <!-- In the head section, update the style -->
    <style>
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border-radius: 10px;
            background: white;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header h1 {
            color: #5e35b1;
            font-size: 24px;
            margin-bottom: 10px;
        }
        .form-control:focus {
            border-color: #5e35b1;
            box-shadow: 0 0 0 0.2rem rgba(94, 53, 177, 0.25);
        }
        .btn-primary {
            background-color: #5e35b1;
            border-color: #5e35b1;
        }
        .btn-primary:hover {
            background-color: #4527a0;
            border-color: #4527a0;
        }
        #webcam-container {
            width: 320px;
            height: 240px;
            margin: 0 auto 20px;
            border: 1px solid #ddd;
            position: relative;
        }
        #capture-btn {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10;
            display: none;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <h1>Margdarshan Admin</h1>
                <p>Please login to continue</p>
            </div>
            <!-- In the form section, add capture button -->
            <!-- <div id="webcam-container">
                <video id="webcam" autoplay playsinline width="320" height="240"></video>
                <canvas id="canvas" style="display: none;"></canvas>
                <button type="button" id="capture-btn" class="btn btn-sm btn-info">
                    <i class="fas fa-camera"></i> Capture
                </button>
            </div> -->
            <form id="loginForm">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
           
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- Replace the JavaScript section -->
    <script>
    $(document).ready(function() {
        let stream;
        //let imageCaptured = false;

        // Initialize webcam
        // async function initWebcam() {
        //     try {
        //         stream = await navigator.mediaDevices.getUserMedia({ video: true });
        //         document.getElementById('webcam').srcObject = stream;
        //         $('#capture-btn').show();
        //     } catch (err) {
        //         toastr.error('Unable to access webcam');
        //     }
        // }

       // initWebcam();

        // Handle image capture
        // $('#capture-btn').on('click', function() {
        //     const video = document.getElementById('webcam');
        //     const canvas = document.getElementById('canvas');
        //     canvas.width = video.videoWidth;
        //     canvas.height = video.videoHeight;
        //     canvas.getContext('2d').drawImage(video, 0, 0);
        //     imageCaptured = true;
        //     toastr.success('Image captured successfully!');
        // });

        // Form submission
        $('#loginForm').on('submit', function(e) {
            e.preventDefault();
            
            // if (!imageCaptured) {
            //     toastr.error('Please capture your image first');
            //     return;
            // }

            //const canvas = document.getElementById('canvas');
            //const imageData = canvas.toDataURL('image/jpeg');

            $.ajax({
                url: '../api/auth/login.php',
                type: 'POST',
                data: {
                    username: $('#username').val(),
                    password: $('#password').val(),
                    //loginImage: imageData
                },
                success: function(response) {
                    if(response.status === 'success') {
                        if (stream) {
                            stream.getTracks().forEach(track => track.stop());
                        }
                        toastr.success('Login successful!');
                        setTimeout(function() {
                            window.location.href = 'dashboard/dashboard.php';
                        }, 1000);
                    } else {
                        toastr.error(response.message || 'Login failed!');
                    }
                },
                error: function() {
                    toastr.error('Something went wrong!');
                }
            });
        });
    });
    </script>
</body>
</html>