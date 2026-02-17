#!/bin/bash

# API Testing Script for Leader Data Service
# Base URL
BASE_URL="http://localhost:8000/api"

echo "================================"
echo "Leader Data Service API Tests"
echo "================================"
echo ""

# Test 1: Get Storage Stats
echo "1. Testing GET /api/storage/stats"
echo "-----------------------------------"
curl -s "$BASE_URL/storage/stats" | python3 -m json.tool
echo ""
echo ""

# Test 2: List Files
echo "2. Testing GET /api/files"
echo "-----------------------------------"
curl -s "$BASE_URL/files" | python3 -m json.tool | head -20
echo "..."
echo ""
echo ""

# Test 3: Upload a file (create a test file first)
echo "3. Testing POST /api/files (Upload)"
echo "-----------------------------------"
echo "Creating test file..."
echo "This is a test document for API testing." > /tmp/test-upload.txt

curl -s -X POST "$BASE_URL/files" \
  -F "file=@/tmp/test-upload.txt" \
  -F "uploaded_by=Test User" \
  -F "description=Test file uploaded via script" \
  -F "category=test" | python3 -m json.tool

# Get the file ID from the response
FILE_ID=$(curl -s "$BASE_URL/files" | python3 -c "import sys, json; data=json.load(sys.stdin); print(data['data'][0]['id'] if data['data'] else 'none')")
echo ""
echo ""

# Test 4: Get specific file
if [ "$FILE_ID" != "none" ]; then
    echo "4. Testing GET /api/files/{id}"
    echo "-----------------------------------"
    curl -s "$BASE_URL/files/$FILE_ID" | python3 -m json.tool
    echo ""
    echo ""

    # Test 5: Update file
    echo "5. Testing PUT /api/files/{id}"
    echo "-----------------------------------"
    curl -s -X PUT "$BASE_URL/files/$FILE_ID" \
      -H "Content-Type: application/json" \
      -d '{"description":"Updated description","category":"updated"}' | python3 -m json.tool
    echo ""
    echo ""

    # Test 6: Download file
    echo "6. Testing GET /api/files/{id}/download"
    echo "-----------------------------------"
    echo "Downloading file to /tmp/downloaded-file.txt..."
    curl -s "$BASE_URL/files/$FILE_ID/download" -o /tmp/downloaded-file.txt
    echo "Download complete. File size: $(wc -c < /tmp/downloaded-file.txt) bytes"
    echo ""
    echo ""

    # Test 7: Delete file
    echo "7. Testing DELETE /api/files/{id}"
    echo "-----------------------------------"
    curl -s -X DELETE "$BASE_URL/files/$FILE_ID" | python3 -m json.tool
    echo ""
else
    echo "No files to test individual operations"
fi

echo ""
echo "================================"
echo "All tests completed!"
echo "================================"
