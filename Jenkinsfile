pipeline {
    agent {
        label 'jenkins-agent'
    }

    environment {
        DOCKER_BUILDKIT   = '1'
        IMAGE_TAG         = "${env.BUILD_NUMBER}"
        IMAGE_NAME        = credentials('perfume-image-name')
        IP_SERVER         = credentials('server-ip')
        SERVER_SSH_DEPLOY = credentials('server-ssh-key')
    }

    stages {

        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Build Image') {
            steps {
                script {
                    echo 'Pulling latest image for layer cache...'
                    sh 'docker pull $IMAGE_NAME:latest || true'

                    echo 'Building Docker image...'
                    sh '''
                        DOCKER_BUILDKIT=1 docker build \
                            --build-arg BUILDKIT_INLINE_CACHE=1 \
                            --cache-from $IMAGE_NAME:latest \
                            -t $IMAGE_NAME:latest \
                            -t $IMAGE_NAME:$IMAGE_TAG \
                            .
                    '''
                }
            }
        }

        stage('Push to Registry') {
            steps {
                script {
                    withCredentials([usernamePassword(
                        credentialsId: 'dockerhub-creds',
                        usernameVariable: 'DOCKER_USERNAME',
                        passwordVariable: 'DOCKER_PASSWORD'
                    )]) {
                        echo 'Logging into Docker Hub...'
                        sh 'echo "$DOCKER_PASSWORD" | docker login -u "$DOCKER_USERNAME" --password-stdin'

                        echo 'Pushing images to registry...'
                        sh 'docker push $IMAGE_NAME:latest'
                        sh 'docker push $IMAGE_NAME:$IMAGE_TAG'

                        echo 'Logging out from Docker Hub...'
                        sh 'docker logout'
                    }
                }
            }
        }

        stage('Deploy to Server') {
            steps {
                script {
                    sshagent(credentials: [env.SERVER_SSH_DEPLOY]) {
                        sh '''
                            ssh -o StrictHostKeyChecking=no $IP_SERVER 'bash -s' \
                                < docker/deploy.sh "$IMAGE_NAME" "$IMAGE_TAG"
                        '''
                    }
                }
            }
        }
    }

    post {
        always {
            script {
                echo 'Cleaning up local Docker images on Jenkins agent...'
                sh 'docker rmi $IMAGE_NAME:latest $IMAGE_NAME:$IMAGE_TAG || true'
                sh "docker image prune -f --filter 'until=24h' || true"
            }
        }

        success {
            script {
                def commitMsg    = sh(script: 'git log -1 --pretty=%B', returnStdout: true).trim()
                def commitAuthor = sh(script: 'git log -1 --pretty=%an', returnStdout: true).trim()
                def currentTime  = new Date().format("yyyy-MM-dd HH:mm:ss")
                def buildNumber  = env.BUILD_NUMBER
                def imageTag     = env.IMAGE_TAG

                commitMsg = commitMsg.replace('"', '\\"').replace('\n', '\\n')

                withCredentials([string(credentialsId: 'discord-webhook-url', variable: 'DISCORD_WEBHOOK')]) {
                    sh """
                        curl -s -H "Content-Type: application/json" -X POST -d '{
                            "embeds": [{
                                "title": "✅ Triển khai thành công",
                                "description": "Dự án **perfume-client** đã được cập nhật thành công lên server! 🚀",
                                "color": 3066993,
                                "fields": [
                                    { "name": "🔢 Build Number",   "value": "#${buildNumber}",  "inline": true },
                                    { "name": "🏷️ Docker Tag",     "value": "${imageTag}",      "inline": true },
                                    { "name": "👤 Commit Author",  "value": "${commitAuthor}",  "inline": true },
                                    { "name": "💬 Commit Message", "value": "${commitMsg}",     "inline": false }
                                ],
                                "footer": { "text": "Jenkins Pipeline • ${currentTime}" }
                            }]
                        }' \$DISCORD_WEBHOOK
                    """
                }
            }
        }

        failure {
            script {
                def commitMsg    = sh(script: 'git log -1 --pretty=%B', returnStdout: true).trim()
                def commitAuthor = sh(script: 'git log -1 --pretty=%an', returnStdout: true).trim()
                def currentTime  = new Date().format("yyyy-MM-dd HH:mm:ss")
                def buildNumber  = env.BUILD_NUMBER
                def imageTag     = env.IMAGE_TAG

                commitMsg = commitMsg.replace('"', '\\"').replace('\n', '\\n')

                withCredentials([string(credentialsId: 'discord-webhook-url', variable: 'DISCORD_WEBHOOK')]) {
                    sh """
                        curl -s -H "Content-Type: application/json" -X POST -d '{
                            "embeds": [{
                                "title": "❌ Triển khai thất bại",
                                "description": "Quá trình CI/CD dự án **perfume-client** gặp lỗi! Vui lòng kiểm tra console log Jenkins.",
                                "color": 15158332,
                                "fields": [
                                    { "name": "🔢 Build Number",   "value": "#${buildNumber}",  "inline": true },
                                    { "name": "🏷️ Docker Tag",     "value": "${imageTag}",      "inline": true },
                                    { "name": "👤 Commit Author",  "value": "${commitAuthor}",  "inline": true },
                                    { "name": "💬 Commit Message", "value": "${commitMsg}",     "inline": false }
                                ],
                                "footer": { "text": "Jenkins Pipeline • ${currentTime}" }
                            }]
                        }' \$DISCORD_WEBHOOK
                    """
                }
            }
        }
    }
}
