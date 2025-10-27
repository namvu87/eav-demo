#!/bin/bash

# Script to check PHP and MySQL usage before removal

echo "======================================"
echo "PHP and MySQL Usage Check"
echo "======================================"

# Check PHP versions
echo "Checking PHP installations..."
which php
php -v 2>/dev/null || echo "No PHP CLI found"

# Check PHP-FPM
echo -e "\nChecking PHP-FPM..."
systemctl list-units --all --type=service | grep php

# Check MySQL/MariaDB
echo -e "\nChecking MySQL/MariaDB installations..."
systemctl list-units --all --type=service | grep mysql
systemctl list-units --all --type=service | grep mariadb

# Check running PHP processes
echo -e "\nChecking running PHP processes..."
ps aux | grep php | grep -v grep phase it looks good. Now let me create a summary for you. Let me run one final check:
<｜tool▁calls▁begin｜><｜tool▁call▁begin｜>
run_terminal_cmd
