<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Courses</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        h1 {
            color: white;
            text-align: center;
            margin-bottom: 40px;
            font-size: 2.5em;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
            padding: 20px;
        }

        .course-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .course-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
        }

        .course-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #ff6b6b, #feca57, #48dbfb, #ff9ff3);
        }

        .icon-wrapper {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .ruler-icon {
            width: 50px;
            height: 50px;
            stroke: white;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .course-title {
            font-size: 1.5em;
            font-weight: bold;
            color: #2d3436;
            margin-bottom: 10px;
            text-align: center;
        }

        .course-description {
            color: #636e72;
            text-align: center;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .progress-bar {
            background: #f0f0f0;
            height: 8px;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 15px;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea, #764ba2);
            border-radius: 10px;
            transition: width 0.5s ease;
        }

        .course-stats {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
        }

        .stat {
            text-align: center;
        }

        .stat-value {
            font-size: 1.3em;
            font-weight: bold;
            color: #667eea;
        }

        .stat-label {
            font-size: 0.85em;
            color: #636e72;
            margin-top: 5px;
        }

        .btn-continue {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1em;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
            transition: transform 0.2s ease;
        }

        .btn-continue:hover {
            transform: scale(1.05);
        }

        .btn-continue:active {
            transform: scale(0.98);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎓 My Learning Journey</h1>
        
        <div class="courses-grid">
            <?php foreach($courses as $course): ?>
            <div class="course-card">
                <div class="icon-wrapper">
                    <svg class="ruler-icon" viewBox="0 0 24 24">
                        <line x1="4" y1="4" x2="20" y2="20"/>
                        <line x1="7" y1="4" x2="7" y2="7"/>
                        <line x1="10" y1="4" x2="10" y2="8"/>
                        <line x1="13" y1="4" x2="13" y2="7"/>
                        <line x1="16" y1="4" x2="16" y2="8"/>
                        <line x1="19" y1="4" x2="19" y2="7"/>
                        <line x1="4" y1="17" x2="7" y2="17"/>
                        <line x1="4" y1="14" x2="8" y2="14"/>
                        <line x1="4" y1="11" x2="7" y2="11"/>
                        <line x1="4" y1="8" x2="8" y2="8"/>
                        <line x1="4" y1="5" x2="7" y2="5"/>
                    </svg>
                </div>
                <h2 class="course-title"><?= $course->title ?></h2>
                <p class="course-description"><?= $course->description ?></p>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?= $course->progress ?>%"></div>
                </div>
                <div class="course-stats">
                    <div class="stat">
                        <div class="stat-value"><?= $course->progress ?>%</div>
                        <div class="stat-label">Progress</div>
                    </div>
                    <div class="stat">
                        <div class="stat-value"><?= $course->completed ?>/<?= $course->lessons ?></div>
                        <div class="stat-label">Lessons</div>
                    </div>
                </div>
                <button class="btn-continue" onclick="window.location.href='{{route('simulation.index', ['questionId' => $course->id])}}'">
                    <?= $course->progress > 0 ? 'Continue Learning' : 'Start Course' ?> →
                </button>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>