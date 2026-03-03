pipeline {
    agent any
    
    environment {
        DOCKER_COMPOSE_PROJECT = 'laravel-docker'
        LOCAL_DEPLOY_PATH = 'D:\\Priya\\inventory-mini\\laravel-docker'  // Your path
    }
    
    stages {

        
        stage('Build Docker') {
            steps {
                bat 'docker compose build --no-cache'
                bat 'docker compose up -d --wait'
            }
        }
        
        stage('Test') {
            steps {
                bat 'docker compose exec -T app php artisan test'
                bat 'docker compose exec -T app php artisan migrate --force'
            }
        }
        
        stage('Deploy Local') {
            when { branch 'main' }
            steps {
                bat '''
                    docker compose down
                    docker compose up -d --build
                    docker compose exec -T app php artisan migrate --force
                    docker compose exec -T app php artisan optimize
                '''
            }
        }
    }
    
    post {
        always {
            bat 'docker compose logs --tail=50 > pipeline-logs.txt'
            cleanWs()
        }
        success {
            echo '🚀 Laravel Docker deployed! http://localhost:8003'
        }
        failure {
            echo '❌ Pipeline failed. Check docker compose logs.'
            bat 'docker compose down'
        }
    }
}
