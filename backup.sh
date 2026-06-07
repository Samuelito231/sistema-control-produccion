#!/bin/bash
pg_dump -U postgres control_produccion > backups/control_produccion_$(date +%Y%m%d_%H%M%S).sql


#!/bin/bash
pg_dump -U postgres control_produccion > backups/control_produccion_$(date +%Y%m%d_%H%M%S).sql
find backups/ -type f -mtime +30 -delete
