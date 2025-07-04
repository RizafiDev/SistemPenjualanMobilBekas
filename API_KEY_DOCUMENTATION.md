# API Key Authentication Documentation

## Overview

Sistem API Filament telah dilindungi dengan autentikasi API key untuk mencegah akses yang tidak sah. Semua endpoint API memerlukan API key yang valid.

## Setup

### 1. Migrasi Database

Jalankan migrasi untuk membuat tabel API keys:

```bash
php artisan migrate
```

### 2. Membuat API Key

#### Menggunakan Command Line

```bash
# Membuat API key dasar
php artisan api:generate-key "Mobile App Key"

# Membuat API key dengan permissions spesifik
php artisan api:generate-key "Frontend App" --permissions=mobil --permissions=varian --permissions=kategori

# Membuat API key dengan tanggal expired
php artisan api:generate-key "Temporary Key" --expires=2024-12-31
```

#### Menggunakan Filament Admin Panel

1. Login ke admin panel
2. Navigate ke **System > API Keys**
3. Klik **New API Key**
4. Isi form dan submit
5. **PENTING:** Salin API key yang ditampilkan, karena tidak akan ditampilkan lagi

### 3. Menggunakan API Key

API key dapat dikirim dengan salah satu cara berikut:

#### Authorization Header (Recommended)

```bash
curl -H "Authorization: Bearer sk_your_api_key_here" \
     "https://yourapp.com/admin/api/varians"
```

#### X-API-Key Header

```bash
curl -H "X-API-Key: sk_your_api_key_here" \
     "https://yourapp.com/admin/api/varians"
```

#### Query Parameter (Less Secure)

```bash
curl "https://yourapp.com/admin/api/varians?api_key=sk_your_api_key_here"
```

## Available API Endpoints

### Varian (Variants)

-   `GET /admin/api/varians` - List all variants
-   `GET /admin/api/varians/{id}` - Get specific variant
-   `POST /admin/api/varians` - Create new variant
-   `PUT /admin/api/varians/{id}` - Update variant
-   `DELETE /admin/api/varians/{id}` - Delete variant

### Mobil (Cars)

-   `GET /admin/api/mobils` - List all cars
-   `GET /admin/api/mobils/{id}` - Get specific car
-   `POST /admin/api/mobils` - Create new car
-   `PUT /admin/api/mobils/{id}` - Update car
-   `DELETE /admin/api/mobils/{id}` - Delete car

### Kategori (Categories)

-   `GET /admin/api/kategoris` - List all categories
-   `GET /admin/api/kategoris/{id}` - Get specific category
-   `POST /admin/api/kategoris` - Create new category
-   `PUT /admin/api/kategoris/{id}` - Update category
-   `DELETE /admin/api/kategoris/{id}` - Delete category

### Merek (Brands)

-   `GET /admin/api/mereks` - List all brands
-   `GET /admin/api/mereks/{id}` - Get specific brand
-   `POST /admin/api/mereks` - Create new brand
-   `PUT /admin/api/mereks/{id}` - Update brand
-   `DELETE /admin/api/mereks/{id}` - Delete brand

### Stok Mobil (Car Stock)

-   `GET /admin/api/stok-mobils` - List all car stock
-   `GET /admin/api/stok-mobils/{id}` - Get specific car stock
-   `POST /admin/api/stok-mobils` - Create new car stock
-   `PUT /admin/api/stok-mobils/{id}` - Update car stock
-   `DELETE /admin/api/stok-mobils/{id}` - Delete car stock

### Foto Mobil (Car Photos)

-   `GET /admin/api/foto-mobils` - List all car photos
-   `GET /admin/api/foto-mobils/{id}` - Get specific car photo
-   `POST /admin/api/foto-mobils` - Create new car photo
-   `PUT /admin/api/foto-mobils/{id}` - Update car photo
-   `DELETE /admin/api/foto-mobils/{id}` - Delete car photo

### Janji Temu (Appointments)

-   `GET /admin/api/janji-temus` - List all appointments
-   `GET /admin/api/janji-temus/{id}` - Get specific appointment
-   `POST /admin/api/janji-temus` - Create new appointment
-   `PUT /admin/api/janji-temus/{id}` - Update appointment
-   `DELETE /admin/api/janji-temus/{id}` - Delete appointment

### Riwayat Servis (Service History)

-   `GET /admin/api/riwayat-servis` - List all service history
-   `GET /admin/api/riwayat-servis/{id}` - Get specific service history
-   `POST /admin/api/riwayat-servis` - Create new service history
-   `PUT /admin/api/riwayat-servis/{id}` - Update service history
-   `DELETE /admin/api/riwayat-servis/{id}` - Delete service history

## Error Responses

### 401 Unauthorized - Missing API Key

```json
{
    "success": false,
    "message": "API key is required. Please provide a valid API key in the Authorization header (Bearer token) or X-API-Key header."
}
```

### 401 Unauthorized - Invalid API Key

```json
{
    "success": false,
    "message": "Invalid or expired API key."
}
```

### 401 Unauthorized - Inactive API Key

```json
{
    "success": false,
    "message": "API key is inactive or expired."
}
```

### 403 Forbidden - Insufficient Permissions

```json
{
    "success": false,
    "message": "Access denied for resource: mobil"
}
```

## API Key Management

### Listing API Keys

```bash
php artisan api:list-keys
```

### Revoking API Keys

```bash
php artisan api:revoke-key {id}
```

## Security Best Practices

1. **Secure Storage**: Store API keys securely, never in plain text
2. **Use HTTPS**: Always use HTTPS in production
3. **Rotate Keys**: Regularly rotate API keys
4. **Monitor Usage**: Check API key usage regularly
5. **Principle of Least Privilege**: Only grant necessary permissions
6. **Set Expiration**: Use expiration dates for temporary access

## Example Usage

### JavaScript/Axios

```javascript
const apiKey = "sk_your_api_key_here";
const response = await axios.get("/admin/api/varians", {
    headers: {
        Authorization: `Bearer ${apiKey}`,
    },
});
```

### PHP/Guzzle

```php
$client = new \GuzzleHttp\Client();
$response = $client->request('GET', 'https://yourapp.com/admin/api/varians', [
    'headers' => [
        'Authorization' => 'Bearer sk_your_api_key_here'
    ]
]);
```

### Python/Requests

```python
import requests

headers = {
    'Authorization': 'Bearer sk_your_api_key_here'
}
response = requests.get('https://yourapp.com/admin/api/varians', headers=headers)
```

## Testing

Untuk testing, Anda dapat menggunakan seeder untuk membuat API key default:

```bash
php artisan db:seed --class=ApiKeySeeder
```

Ini akan membuat API key default yang dapat digunakan untuk testing.
