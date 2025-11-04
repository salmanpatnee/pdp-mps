# Deployment Documentation

This document provides instructions for deploying the application.

## Queue Worker

The application uses a queue worker to process background jobs, such as sending emails. You need to set up a supervisor to keep the queue worker running.

### Supervisor Configuration

Create a new supervisor configuration file (e.g., `/etc/supervisor/conf.d/manuscript-system.conf`) with the following content:

```
[program:manuscript-system-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/your/project/artisan queue:work --queue=emails --sleep=3 --tries=3
autostart=true
autorestart=true
user=your-user
numprocs=8
redirect_stderr=true
stdout_logfile=/path/to/your/project/storage/logs/worker.log
```

**Note:** Replace `/path/to/your/project` and `your-user` with the actual values for your environment.

After creating the configuration file, you need to reread the supervisor configuration and start the worker:

```bash
supervisorctl reread
supervisorctl update
supervisorctl start manuscript-system-worker:*
```
