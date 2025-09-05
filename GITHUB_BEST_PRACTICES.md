# GitHub Best Practices for Laravel Project

## Overview
This document outlines the best practices for managing and pushing code to GitHub for this Laravel project.

## 1. Repository Setup

### Initial Setup
```bash
# Initialize git repository (if not already done)
git init

# Add remote repository
git remote add origin https://github.com/yourusername/your-laravel-project.git

# Verify remote
git remote -v
```

### Branch Structure
- `main` - Production-ready code
- `develop` - Integration branch for features
- `feature/*` - Individual feature branches
- `hotfix/*` - Critical bug fixes
- `release/*` - Release preparation branches

## 2. Security Best Practices

### Sensitive Files to Ignore
The following files should NEVER be committed to GitHub:

- `.env` - Environment configuration
- `storage/app/google-credentials.json` - Google API credentials
- `storage/app/google-service-account.json` - Google service account
- Any files containing API keys, passwords, or tokens
- `client_secret_*.json` - OAuth client secrets

### Current .gitignore Configuration
Ensure your `.gitignore` includes:

```gitignore
# Environment files
.env
.env.backup
.env.production

# Google credentials
/storage/app/google-credentials.json
/storage/app/google-service-account.json
client_secret_*.json

# Laravel
/vendor
/node_modules
/public/hot
/public/storage
/storage/*.key
/storage/pail
*.env
.env.*
!.env.example

# IDE
.idea/
.vscode/
*.sublime-project
*.sublime-workspace

# OS
.DS_Store
Thumbs.db

# Logs
*.log
npm-debug.log*
yarn-debug.log*
yarn-error.log*
```

## 3. Commit Message Standards

### Format
```
<type>(<scope>): <subject>

<body>

<footer>
```

### Types
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting, etc.)
- `refactor`: Code refactoring
- `test`: Adding or modifying tests
- `chore`: Maintenance tasks

### Examples
```
feat(auth): add Google OAuth integration

- Implement Google OAuth2 flow
- Add user authentication middleware
- Update user model with Google ID field

Closes #123
```

## 4. Git Workflow

### Feature Development Workflow
```bash
# 1. Start from develop branch
git checkout develop
git pull origin develop

# 2. Create feature branch
git checkout -b feature/user-authentication

# 3. Make changes and commit
git add .
git commit -m "feat(auth): implement user registration"

# 4. Push feature branch
git push origin feature/user-authentication

# 5. Create Pull Request on GitHub
```

### Hotfix Workflow
```bash
# 1. Create hotfix from main
git checkout main
git checkout -b hotfix/critical-security-fix

# 2. Make fix and commit
git add .
git commit -m "fix(security): patch SQL injection vulnerability"

# 3. Push and create PR
git push origin hotfix/critical-security-fix
```

## 5. Pull Request Guidelines

### Before Creating PR
- [ ] Code follows project standards
- [ ] All tests pass
- [ ] Documentation updated
- [ ] No sensitive data committed
- [ ] Branch is up-to-date with target branch

### PR Template
Use the provided PR template in `.github/PULL_REQUEST_TEMPLATE.md`

## 6. Branch Protection Rules

### Required Settings for Main Branch
- Require pull request reviews before merging
- Require status checks to pass before merging
- Require branches to be up to date before merging
- Include administrators in restrictions

### Required Status Checks
- Laravel tests
- Code style checks
- Security scanning

## 7. Deployment Workflow

### Staging Deployment
```bash
# Deploy to staging
git checkout develop
git pull origin develop
git push origin develop:staging
```

### Production Deployment
```bash
# Create release
git checkout main
git pull origin main
git tag -a v1.0.0 -m "Release version 1.0.0"
git push origin main --tags
```

## 8. Security Scanning

### Before Pushing Code
```bash
# Check for sensitive data
git secrets --scan

# Run security checks
composer audit
npm audit

# Check for debugging code
grep -r "dd\|dump\|var_dump" app/
```

## 9. Code Review Checklist

### For Authors
- [ ] Self-review completed
- [ ] Tests added/updated
- [ ] Documentation updated
- [ ] No debugging code left
- [ ] Sensitive data removed

### For Reviewers
- [ ] Code quality acceptable
- [ ] Security vulnerabilities checked
- [ ] Performance implications considered
- [ ] Tests adequate
- [ ] Documentation complete

## 10. Common Commands

### Daily Workflow
```bash
# Start of day
git checkout develop
git pull origin develop

# Create feature branch
git checkout -b feature/new-feature

# During development
git add .
git commit -m "feat(scope): description"

# Push changes
git push origin feature/new-feature

# End of day - create PR
gh pr create --title "feat: add new feature" --body "Description of changes"
```

### Cleanup Commands
```bash
# Clean up local branches
git branch --merged | grep -v main | grep -v develop | xargs -n 1 git branch -d

# Prune remote branches
git remote prune origin
```

## 11. Emergency Procedures

### Accidentally Committed Sensitive Data
```bash
# If not pushed yet
git reset --soft HEAD~1

# If already pushed
git revert <commit-hash>
# Then force push (use with caution)
git push origin main --force-with-lease
```

### Rollback Production
```bash
# Quick rollback
git revert <bad-commit-hash>
git push origin main
```

## 12. Useful Tools

### GitHub CLI
```bash
# Install GitHub CLI
gh auth login
gh repo create
gh pr create
gh pr merge
```

### Git Hooks
Set up pre-commit hooks to prevent sensitive data commits:
```bash
# Install pre-commit
pip install pre-commit
pre-commit install
```

This guide ensures secure, professional code management for your Laravel project on GitHub.