// API Configuration
export const API_BASE_URL =
    process.env.NEXT_PUBLIC_API_BASE_URL || "http://127.0.0.1:8000/api/admin";

// Import types for transformation
import type { Mobil, FotoMobil } from "./types";

// API Endpoints
export const API_ENDPOINTS = {
    FOTO_MOBILS: "/foto-mobils",
    JANJI_TEMUS: "/janji-temus",
    KATEGORIS: "/kategoris",
    MEREKS: "/mereks",
    MOBILS: "/mobils",
    RIWAYAT_SERVIS: "/riwayat-servis",
    STOK_MOBILS: "/stok-mobils",
    VARIANS: "/varians", // ✅ Added missing endpoint
} as const;

// API Helper Functions
export const buildApiUrl = (endpoint: string): string => {
    return `${API_BASE_URL}${endpoint}`;
};

// Image URL helper
export const getImageUrl = (path: string): string => {
    if (!path) return "/images/car-placeholder.jpg";

    // If already a full URL, return as is
    if (path.startsWith("http://") || path.startsWith("https://")) {
        return path;
    }

    // If starts with /, return as is (already relative to domain)
    if (path.startsWith("/")) {
        return path;
    }

    // For storage paths, prefix with storage URL
    const STORAGE_BASE_URL =
        process.env.NEXT_PUBLIC_STORAGE_URL || "http://127.0.0.1:8000/storage";
    const fullUrl = `${STORAGE_BASE_URL}/${path}`;

    // Debug log in development
    if (process.env.NODE_ENV === "development") {
        console.log("Image URL transform:", { path, fullUrl });
    }

    return fullUrl;
};

// Data transformation utilities to handle API response differences
export const transformMobilData = (mobil: any): Mobil => {
    return {
        ...mobil,
        // Handle legacy field names for backward compatibility
        nama_mobil: mobil.nama || mobil.nama_mobil,
        tahun_produksi: mobil.tahun_mulai || mobil.tahun_produksi,
        spesifikasi: mobil.fitur_unggulan || mobil.spesifikasi,
    };
};

export const transformFotoMobilData = (foto: any): FotoMobil => {
    return {
        ...foto,
        // Handle different field names and ensure proper URL format
        url_foto: getImageUrl(foto.path_file || foto.url_foto),
        path_file: getImageUrl(foto.path_file || foto.url_foto),
        deskripsi: foto.keterangan || foto.deskripsi,
        // Determine if primary photo based on urutan_tampil
        is_primary: foto.urutan_tampil === 1 || foto.is_primary || false,
    };
};

// Fetch wrapper with error handling
export const apiFetch = async <T>(
    url: string,
    options?: RequestInit
): Promise<T> => {
    try {
        const response = await fetch(url, {
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                ...options?.headers,
            },
            ...options,
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        return data;
    } catch (error) {
        console.error("API Error:", error);
        throw error;
    }
};

// Enhanced fetch wrapper with data transformation and error handling
export const apiSafeFetch = async <T>(
    url: string,
    options?: RequestInit
): Promise<T> => {
    try {
        const response = await fetch(url, {
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                ...options?.headers,
            },
            ...options,
        });

        if (!response.ok) {
            // Enhanced error handling
            const errorText = await response.text();
            console.error(`API Error [${response.status}]:`, errorText);

            // Try to parse error as JSON
            try {
                const errorJson = JSON.parse(errorText);
                throw new Error(
                    errorJson.message ||
                        `HTTP error! status: ${response.status}`
                );
            } catch {
                throw new Error(
                    `HTTP error! status: ${response.status} - ${errorText}`
                );
            }
        }

        const data = await response.json();

        // Log the actual API response structure for debugging
        if (process.env.NODE_ENV === "development") {
            console.log("API Response:", {
                url,
                status: response.status,
                data: data,
            });
        }

        return data;
    } catch (error) {
        console.error("API Error:", error);
        throw error;
    }
};
