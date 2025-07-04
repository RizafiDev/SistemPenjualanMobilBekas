# Frontend Integration Guide

## Menggunakan API Key di Frontend

### 1. JavaScript/Fetch API

```javascript
// Config API
const API_BASE_URL = "https://yourapp.com/admin/api";
const API_KEY = "sk_your_api_key_here"; // Simpan di environment variable

// Function untuk call API
async function callAPI(endpoint, options = {}) {
    const url = `${API_BASE_URL}${endpoint}`;
    const config = {
        ...options,
        headers: {
            Authorization: `Bearer ${API_KEY}`,
            "Content-Type": "application/json",
            ...options.headers,
        },
    };

    try {
        const response = await fetch(url, config);

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        console.error("API Error:", error);
        throw error;
    }
}

// Contoh penggunaan
async function getMobils() {
    try {
        const data = await callAPI("/mobils");
        console.log("Mobils:", data);
        return data;
    } catch (error) {
        console.error("Error getting mobils:", error);
    }
}

async function createMobil(mobilData) {
    try {
        const data = await callAPI("/mobils", {
            method: "POST",
            body: JSON.stringify(mobilData),
        });
        console.log("Created mobil:", data);
        return data;
    } catch (error) {
        console.error("Error creating mobil:", error);
    }
}
```

### 2. Axios (Recommended)

```javascript
import axios from "axios";

// Create axios instance
const api = axios.create({
    baseURL: "https://yourapp.com/admin/api",
    headers: {
        Authorization: `Bearer ${process.env.REACT_APP_API_KEY}`,
        "Content-Type": "application/json",
    },
});

// Request interceptor
api.interceptors.request.use(
    (config) => {
        // Add API key to all requests
        config.headers.Authorization = `Bearer ${process.env.REACT_APP_API_KEY}`;
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

// Response interceptor
api.interceptors.response.use(
    (response) => {
        return response;
    },
    (error) => {
        if (error.response?.status === 401) {
            console.error("API Key invalid or expired");
            // Handle unauthorized access
        }
        return Promise.reject(error);
    }
);

// Export API functions
export const mobilAPI = {
    getAll: () => api.get("/mobils"),
    getById: (id) => api.get(`/mobils/${id}`),
    create: (data) => api.post("/mobils", data),
    update: (id, data) => api.put(`/mobils/${id}`, data),
    delete: (id) => api.delete(`/mobils/${id}`),
};

export const varianAPI = {
    getAll: () => api.get("/varians"),
    getById: (id) => api.get(`/varians/${id}`),
    create: (data) => api.post("/varians", data),
    update: (id, data) => api.put(`/varians/${id}`, data),
    delete: (id) => api.delete(`/varians/${id}`),
};
```

### 3. React Hook

```javascript
import { useState, useEffect } from "react";
import { mobilAPI } from "./api";

// Custom hook untuk mobil data
export function useMobils() {
    const [mobils, setMobils] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        loadMobils();
    }, []);

    const loadMobils = async () => {
        try {
            setLoading(true);
            const response = await mobilAPI.getAll();
            setMobils(response.data);
            setError(null);
        } catch (err) {
            setError(err.message);
            console.error("Error loading mobils:", err);
        } finally {
            setLoading(false);
        }
    };

    const createMobil = async (mobilData) => {
        try {
            const response = await mobilAPI.create(mobilData);
            setMobils([...mobils, response.data]);
            return response.data;
        } catch (err) {
            setError(err.message);
            throw err;
        }
    };

    const updateMobil = async (id, mobilData) => {
        try {
            const response = await mobilAPI.update(id, mobilData);
            setMobils(mobils.map((m) => (m.id === id ? response.data : m)));
            return response.data;
        } catch (err) {
            setError(err.message);
            throw err;
        }
    };

    const deleteMobil = async (id) => {
        try {
            await mobilAPI.delete(id);
            setMobils(mobils.filter((m) => m.id !== id));
        } catch (err) {
            setError(err.message);
            throw err;
        }
    };

    return {
        mobils,
        loading,
        error,
        loadMobils,
        createMobil,
        updateMobil,
        deleteMobil,
    };
}
```

### 4. Environment Variables

#### .env file

```bash
# Frontend environment
REACT_APP_API_KEY=sk_your_api_key_here
REACT_APP_API_BASE_URL=https://yourapp.com/admin/api

# Next.js environment
NEXT_PUBLIC_API_KEY=sk_your_api_key_here
NEXT_PUBLIC_API_BASE_URL=https://yourapp.com/admin/api
```

#### Environment config

```javascript
// config/api.js
export const API_CONFIG = {
    baseURL: process.env.REACT_APP_API_BASE_URL || "http://localhost/admin/api",
    apiKey: process.env.REACT_APP_API_KEY,
    timeout: 10000,
};
```

### 5. Error Handling

```javascript
// Error handler utility
export function handleAPIError(error) {
    if (error.response) {
        // Server responded with error status
        const { status, data } = error.response;

        switch (status) {
            case 401:
                return {
                    type: "UNAUTHORIZED",
                    message:
                        "API key tidak valid atau expired. Silakan perbarui API key.",
                    shouldRefreshKey: true,
                };
            case 403:
                return {
                    type: "FORBIDDEN",
                    message:
                        "Akses ditolak. API key tidak memiliki permission untuk resource ini.",
                    shouldRequestPermission: true,
                };
            case 429:
                return {
                    type: "RATE_LIMIT",
                    message: "Terlalu banyak request. Silakan coba lagi nanti.",
                    shouldRetry: true,
                };
            default:
                return {
                    type: "API_ERROR",
                    message: data?.message || "Terjadi kesalahan pada server.",
                    shouldRetry: false,
                };
        }
    } else if (error.request) {
        // Network error
        return {
            type: "NETWORK_ERROR",
            message: "Koneksi internet bermasalah. Silakan cek koneksi Anda.",
            shouldRetry: true,
        };
    } else {
        // Other error
        return {
            type: "UNKNOWN_ERROR",
            message: "Terjadi kesalahan yang tidak diketahui.",
            shouldRetry: false,
        };
    }
}
```

### 6. Security Best Practices

#### DO's:

-   Store API key di environment variables
-   Use HTTPS untuk semua API calls
-   Implement proper error handling
-   Log API usage untuk monitoring
-   Rotate API keys secara berkala
-   Use interceptors untuk centralized auth

#### DON'Ts:

-   Jangan hardcode API key di code
-   Jangan commit API key ke version control
-   Jangan expose API key di client-side logging
-   Jangan gunakan API key di URL parameters (kecuali untuk testing)

### 7. Testing di Frontend

```javascript
// Test API connection
async function testAPIConnection() {
    try {
        const response = await fetch("/admin/api/varians?limit=1", {
            headers: {
                Authorization: `Bearer ${API_KEY}`,
            },
        });

        if (response.ok) {
            console.log("✅ API connection successful");
            return true;
        } else {
            console.error("❌ API connection failed:", response.status);
            return false;
        }
    } catch (error) {
        console.error("❌ Network error:", error);
        return false;
    }
}
```

## Implementasi di Next.js

### API Route Handler

```javascript
// pages/api/mobils.js atau app/api/mobils/route.js
export async function GET() {
    try {
        const response = await fetch(`${process.env.API_BASE_URL}/mobils`, {
            headers: {
                Authorization: `Bearer ${process.env.API_KEY}`,
            },
        });

        const data = await response.json();
        return Response.json(data);
    } catch (error) {
        return Response.json(
            { error: "Failed to fetch mobils" },
            { status: 500 }
        );
    }
}
```

### Server-Side Rendering

```javascript
// pages/mobils.js
export async function getServerSideProps() {
    try {
        const response = await fetch(`${process.env.API_BASE_URL}/mobils`, {
            headers: {
                Authorization: `Bearer ${process.env.API_KEY}`,
            },
        });

        const mobils = await response.json();

        return {
            props: {
                mobils,
            },
        };
    } catch (error) {
        return {
            props: {
                mobils: [],
                error: "Failed to load mobils",
            },
        };
    }
}
```
