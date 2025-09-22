<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit;
}
$exam_id = intval($_GET['exam_id'] ?? 0);
if ($exam_id <= 0) {
    header("Location: user_dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Exam</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7ff, #ffffff);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .header {
            background: linear-gradient(135deg, #4a6cf7, #667eea);
            color: white;
            padding: 20px;
            width: 100%;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        
        .exam-container {
            width: 800px;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        
        .question-box {
            border: 1px solid #e9ecef;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .question-text {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: #495057;
        }

        .media-container {
            margin-bottom: 15px;
            text-align: center;
        }
        .media-container img, .media-container video, .media-container audio {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }
        
        .options-list {
            list-style: none;
            padding: 0;
        }
        
        .options-list li {
            margin-bottom: 10px;
        }
        
        .options-list input[type="radio"] {
            margin-right: 10px;
        }

        .timer {
            font-size: 1.2rem;
            font-weight: bold;
            color: #dc3545;
            text-align: right;
            margin-bottom: 20px;
        }

        .action-buttons {
            display: flex;
            justify-content: space-between;
        }

        .action-buttons button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            color: white;
            font-weight: bold;
        }

        #next-btn {
            background: #4a6cf7;
        }

        #submit-btn {
            background: #28a745;
        }
    </style>
</head>
<body>
    <header class="header">
        <h1>NEW FOLDER 5</h1>
    </header>
    
    <div class="exam-container">
        <div id="question-area">
            </div>
        <div class="action-buttons">
            <button id="next-btn" style="display:none;">Next Question</button>
            <button id="submit-btn" style="display:none;">Submit Exam</button>
        </div>
    </div>
    
    <script>
        const examId = <?php echo $exam_id; ?>;
        const questionArea = document.getElementById('question-area');
        const nextBtn = document.getElementById('next-btn');
        const submitBtn = document.getElementById('submit-btn');
        
        let questions = [];
        let currentQuestionIndex = 0;
        let userAnswers = {};
        let timer;
        const TIME_PER_QUESTION = 30;

        async function fetchQuestions() {
            try {
                const res = await fetch(`list_user_questions.php?exam_id=${examId}`);
                const data = await res.json();
                if (data.status === 'success' && data.questions.length > 0) {
                    questions = data.questions;
                    displayQuestion();
                } else {
                    questionArea.innerHTML = '<p>No questions found for this exam.</p>';
                }
            } catch (err) {
                console.error('Error fetching questions:', err);
                questionArea.innerHTML = '<p>Failed to load questions. Please try again.</p>';
            }
        }

        function getYouTubeEmbedUrl(url) {
            const regex = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i;
            const match = url.match(regex);
            return match ? `https://www.youtube.com/embed/${match[1]}?enablejsapi=1` : null;
        }

        function displayQuestion() {
            if (currentQuestionIndex >= questions.length) {
                questionArea.innerHTML = '<p>You have reached the end of the exam. Click Submit to finish.</p>';
                nextBtn.style.display = 'none';
                submitBtn.style.display = 'block';
                clearTimeout(timer);
                return;
            }

            const q = questions[currentQuestionIndex];
            
            let mediaHtml = '';
            let isMediaQuestion = ['audio', 'video'].includes(q.q_type);
            
            if (q.media_url) {
                if (q.q_type === 'audio') {
                    mediaHtml = `<div class="media-container">
                                    <p>The timer will start after the audio finishes.</p>
                                    <audio src="${q.media_url}" controls id="media-player"></audio>
                                </div>`;
                } else if (q.q_type === 'video') {
                    const youtubeEmbedUrl = getYouTubeEmbedUrl(q.media_url);
                    if (youtubeEmbedUrl) {
                        mediaHtml = `<div class="media-container">
                                        <p>The timer will start after the video finishes.</p>
                                        <iframe id="media-player" width="560" height="315" src="${youtubeEmbedUrl}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                    </div>`;
                    } else {
                        mediaHtml = `<div class="media-container">
                                        <p>The timer will start after the video finishes.</p>
                                        <video src="${q.media_url}" controls id="media-player" width="100%"></video>
                                    </div>`;
                    }
                } else if (q.q_type === 'image') {
                    mediaHtml = `<div class="media-container">
                                    <img src="${q.media_url}" alt="Question Image">
                                </div>`;
                }
            }
            
            let optionsHtml = '';
            if (q.q_type === 'truefalse') {
                optionsHtml = `<ul class="options-list">
                                <li><input type="radio" name="option" value="1" id="opt1"> <label for="opt1">True</label></li>
                                <li><input type="radio" name="option" value="0" id="opt2"> <label for="opt2">False</label></li>
                              </ul>`;
            } else if (['mcq', 'audio', 'video', 'image'].includes(q.q_type)) {
                optionsHtml = `<ul class="options-list">
                                ${q.options.map((opt, i) => 
                                    `<li><input type="radio" name="option" value="${opt.option_index}" id="opt${i + 1}"> <label for="opt${i + 1}">${opt.option_text}</label></li>`
                                ).join('')}
                              </ul>`;
            }

            questionArea.innerHTML = `
                <div class="timer" id="timer-display"></div>
                <div class="question-box">
                    <p class="question-text">Q${currentQuestionIndex + 1}: ${q.question_text}</p>
                    ${mediaHtml}
                    <form id="question-form">
                        ${optionsHtml}
                    </form>
                </div>
            `;
            
            if (isMediaQuestion) {
                nextBtn.style.display = 'none';

                if(q.q_type === 'video' && getYouTubeEmbedUrl(q.media_url)){
                     // Correctly load the YouTube IFrame Player API
                     if(window.YT && window.YT.Player){
                        new YT.Player('media-player', {
                            events: {
                                'onStateChange': (event) => {
                                    if (event.data === YT.PlayerState.ENDED) {
                                        startTimer(TIME_PER_QUESTION);
                                        nextBtn.style.display = 'block';
                                    }
                                }
                            }
                        });
                     } else {
                         window.onYouTubeIframeAPIReady = () => {
                             new YT.Player('media-player', {
                                events: {
                                    'onStateChange': (event) => {
                                        if (event.data === YT.PlayerState.ENDED) {
                                            startTimer(TIME_PER_QUESTION);
                                            nextBtn.style.display = 'block';
                                        }
                                    }
                                }
                             });
                         };
                         const tag = document.createElement('script');
                         tag.src = "https://www.youtube.com/iframe_api";
                         const firstScriptTag = document.getElementsByTagName('script')[0];
                         firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
                     }
                } else {
                    const mediaPlayer = document.getElementById('media-player');
                    if(mediaPlayer) {
                         mediaPlayer.addEventListener('ended', () => {
                            startTimer(TIME_PER_QUESTION);
                            nextBtn.style.display = 'block';
                        }, { once: true });
                    }
                }
            } else {
                startTimer(TIME_PER_QUESTION);
                nextBtn.style.display = 'block';
            }
        }
        
        function startTimer(duration) {
            let timeLeft = duration;
            const timerDisplay = document.getElementById('timer-display');
            if(!timerDisplay) return;

            timerDisplay.textContent = `Time left: ${timeLeft}s`;
            
            clearInterval(timer);
            timer = setInterval(() => {
                timeLeft--;
                timerDisplay.textContent = `Time left: ${timeLeft}s`;
                if (timeLeft <= 0) {
                    clearInterval(timer);
                    moveToNextQuestion();
                }
            }, 1000);
        }
        
        function saveAnswer() {
            const form = document.getElementById('question-form');
            if (form) {
                const selected = form.querySelector('input[name="option"]:checked');
                if (selected) {
                    userAnswers[questions[currentQuestionIndex].id] = selected.value;
                }
            }
        }

        function moveToNextQuestion() {
            clearInterval(timer);
            saveAnswer();
            currentQuestionIndex++;
            displayQuestion();
        }

        nextBtn.addEventListener('click', moveToNextQuestion);
        submitBtn.addEventListener('click', submitExam);

        async function submitExam() {
            saveAnswer();
            
            const fd = new FormData();
            fd.append('exam_id', examId);
            fd.append('answers', JSON.stringify(userAnswers));
            
            try {
                const res = await fetch('submit_exam.php', { method: 'POST', body: fd });
                const data = await res.json();
                
                if (data.status === 'success') {
                    alert('Exam submitted successfully! Your score is: ' + data.score);
                    window.location.href = 'user_dashboard.php';
                } else {
                    alert('Error submitting exam: ' + data.message);
                }
            } catch (err) {
                console.error('Error submitting exam:', err);
                alert('A network or server error occurred.');
            }
        }

        fetchQuestions();
    </script>
</body>
</html>