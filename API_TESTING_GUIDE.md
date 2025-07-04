# Test API Endpoints

## 🔐 Status: API KEY AUTHENTICATION IMPLEMENTED ✅

**All API endpoints now require a valid API key for access.**

## Setup untuk Testing

1. **Buat API Key untuk Testing**

```bash
php artisan api:generate-key "Testing Key"
```

2. **Simpan API Key**
   Export API key ke environment variable:

```bash
export API_KEY="sk_your_generated_key_here"
```

## Test Commands

### Test Varian Endpoints

```bash
# List all varians
curl -H "Authorization: Bearer $API_KEY" \
     "http://127.0.0.1:8000/api/admin/varians"

# Get specific varian
curl -H "Authorization: Bearer $API_KEY" \
     "http://127.0.0.1:8000/api/admin/varians/1"

# Create new varian
curl -X POST \
     -H "Authorization: Bearer $API_KEY" \
     -H "Content-Type: application/json" \
     -d '{"nama":"Test Varian","kode":"TV001","harga_otr":150000000}' \
     "http://127.0.0.1:8000/api/admin/varians"
```

### Test Mobil Endpoints

```bash
# List all mobils
curl -H "Authorization: Bearer $API_KEY" \
     "http://127.0.0.1:8000/api/admin/mobils"

# Get specific mobil
curl -H "Authorization: Bearer $API_KEY" \
     "http://127.0.0.1:8000/api/admin/mobils/1"
```

### Test Kategori Endpoints

```bash
# List all kategoris
curl -H "Authorization: Bearer $API_KEY" \
     "http://127.0.0.1:8000/api/admin/kategoris"

# Get specific kategori
curl -H "Authorization: Bearer $API_KEY" \
     "http://127.0.0.1:8000/api/admin/kategoris/1"
```

### Test Error Responses

#### Test without API Key

```bash
curl "http://127.0.0.1:8000/api/admin/varians"
# Expected: 401 Unauthorized
```

#### Test with invalid API Key

```bash
curl -H "Authorization: Bearer invalid_key" \
     "http://127.0.0.1:8000/api/admin/varians"
# Expected: 401 Unauthorized
```

#### Test with X-API-Key header

```bash
curl -H "X-API-Key: $API_KEY" \
     "http://127.0.0.1:8000/api/admin/varians"
# Expected: 200 OK
```

#### Test with query parameter

```bash
curl "http://127.0.0.1:8000/api/admin/varians?api_key=$API_KEY"
# Expected: 200 OK
```

## Postman Collection

Anda juga dapat menggunakan collection Postman yang sudah ada:
`Penjualan Mobil Bekas.postman_collection.json`

Update collection tersebut dengan menambahkan Authorization header:

-   Type: Bearer Token
-   Token: {{api_key}}

Lalu set variable `api_key` di environment Postman dengan nilai API key yang sudah dibuat.

## Monitoring

Untuk monitoring penggunaan API key:

```bash
# List semua API keys dan usage
php artisan api:list-keys

# Check logs
tail -f storage/logs/laravel.log
```
