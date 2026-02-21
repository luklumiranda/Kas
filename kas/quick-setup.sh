#!/bin/bash
# Quick Setup & Test Script untuk Mobile App API Connection

echo "======================================"
echo "  API Setup & Connection Test"
echo "======================================"

# Color codes
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if Laravel is installed
if [ ! -f "artisan" ]; then
    echo -e "${RED}❌ artisan file tidak ditemukan. Pastikan Anda di folder Laravel.${NC}"
    exit 1
fi

echo -e "${YELLOW}1. Testing database connection...${NC}"
php artisan tinker --execute="DB::connection()->getPDO();" 2>/dev/null
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Database connected${NC}"
else
    echo -e "${RED}❌ Database connection failed${NC}"
    exit 1
fi

echo -e "\n${YELLOW}2. Checking if User exists...${NC}"
php artisan tinker --execute="echo \App\Models\User::count() > 0 ? '✅ Users exist' : '⚠️  No users found';"

echo -e "\n${YELLOW}3. Verifying API routes...${NC}"
php artisan route:list --path=api

echo -e "\n${YELLOW}4. Starting Laravel development server...${NC}"
echo -e "${GREEN}🚀 Server running at http://localhost:8000${NC}"
echo -e "${YELLOW}API Base URL: http://localhost:8000/api${NC}"
echo ""
echo -e "${YELLOW}Tips:${NC}"
echo "- Untuk mobile app di device lain, ganti localhost dengan IP address laptop"
echo "- Gunakan: http://<IP>:8000/api"
echo "- Cek IP dengan: ipconfig (Windows) atau ifconfig (Mac/Linux)"
echo ""
echo "Tekan Ctrl+C untuk stop server"
echo "======================================"

php artisan serve --host=0.0.0.0 --port=8000
