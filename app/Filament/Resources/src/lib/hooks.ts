import useSWR from "swr";
import {
    buildApiUrl,
    apiSafeFetch,
    API_ENDPOINTS,
    transformFotoMobilData,
    getImageUrl,
} from "./api";
import type {
    Mobil,
    Merek,
    Kategori,
    StokMobil,
    FotoMobil,
    RiwayatServis,
    JanjiTemu,
    PaginatedResponse,
    CarSearchFilters,
    Varian,
} from "./types";

// Generic fetcher for SWR using the enhanced API fetch
const fetcher = async <T = unknown>(url: string): Promise<T> => {
    const data = await apiSafeFetch<T>(url);

    // Transform foto data if present
    if (data && typeof data === "object") {
        // Handle single mobil with foto_mobils
        if ("foto_mobils" in data && Array.isArray((data as any).foto_mobils)) {
            (data as any).foto_mobils = (data as any).foto_mobils.map(
                transformFotoMobilData
            );
        }

        // Handle paginated response with mobil data
        if ("data" in data && Array.isArray((data as any).data)) {
            (data as any).data = (data as any).data.map((item: any) => {
                if (item.foto_mobils && Array.isArray(item.foto_mobils)) {
                    item.foto_mobils = item.foto_mobils.map(
                        transformFotoMobilData
                    );
                }
                // Also handle nested mobil data in StokMobil
                if (
                    item.mobil &&
                    item.mobil.foto_mobils &&
                    Array.isArray(item.mobil.foto_mobils)
                ) {
                    item.mobil.foto_mobils = item.mobil.foto_mobils.map(
                        transformFotoMobilData
                    );
                }
                return item;
            });
        }

        // Handle direct foto data
        if (url.includes("foto-mobils")) {
            if ("data" in data && Array.isArray((data as any).data)) {
                (data as any).data = (data as any).data.map(
                    transformFotoMobilData
                );
            } else if (Array.isArray(data)) {
                return (data as any[]).map(transformFotoMobilData) as T;
            }
        }
    }

    return data;
};

// Custom hooks for API data fetching

export const useMobils = (filters?: CarSearchFilters) => {
    const queryParams = filters
        ? new URLSearchParams(
              Object.entries(filters).reduce((acc, [key, value]) => {
                  if (value !== undefined && value !== "") {
                      acc[key] = value.toString();
                  }
                  return acc;
              }, {} as Record<string, string>)
          ).toString()
        : "";

    const url = buildApiUrl(
        `${API_ENDPOINTS.MOBILS}${queryParams ? `?${queryParams}` : ""}`
    );

    return useSWR<PaginatedResponse<Mobil>>(url, fetcher);
};

export const useMobil = (id: string | number) => {
    const url = buildApiUrl(`${API_ENDPOINTS.MOBILS}/${id}`);
    return useSWR(id ? url : null, fetcher);
};

export const useMereks = () => {
    const url = buildApiUrl(API_ENDPOINTS.MEREKS);
    return useSWR<PaginatedResponse<Merek>>(url, fetcher);
};

export const useMerek = (id: string | number) => {
    const url = buildApiUrl(`${API_ENDPOINTS.MEREKS}/${id}`);
    return useSWR(id ? url : null, fetcher);
};

export const useKategoris = () => {
    const url = buildApiUrl(API_ENDPOINTS.KATEGORIS);
    return useSWR<PaginatedResponse<Kategori>>(url, fetcher);
};

export const useKategori = (id: string | number) => {
    const url = buildApiUrl(`${API_ENDPOINTS.KATEGORIS}/${id}`);
    return useSWR(id ? url : null, fetcher);
};

export const useStokMobils = (mobilId?: string | number) => {
    const url = buildApiUrl(
        `${API_ENDPOINTS.STOK_MOBILS}${mobilId ? `?mobil_id=${mobilId}` : ""}`
    );
    return useSWR(url, fetcher);
};

export const useStokMobil = (id: string | number) => {
    const url = buildApiUrl(`${API_ENDPOINTS.STOK_MOBILS}/${id}`);
    return useSWR(id ? url : null, fetcher);
};

export const useFotoMobils = (mobilId?: string | number) => {
    const url = buildApiUrl(
        `${API_ENDPOINTS.FOTO_MOBILS}${mobilId ? `?mobil_id=${mobilId}` : ""}`
    );
    return useSWR(url, fetcher);
};

export const useRiwayatServis = (stokMobilId: string | number) => {
    const url = buildApiUrl(
        `${API_ENDPOINTS.RIWAYAT_SERVIS}?stok_mobil_id=${stokMobilId}`
    );
    return useSWR(stokMobilId ? url : null, fetcher);
};

export const useJanjiTemus = () => {
    const url = buildApiUrl(API_ENDPOINTS.JANJI_TEMUS);
    return useSWR(url, fetcher);
};

export const useVarians = (mobilId?: string | number) => {
    const url = buildApiUrl(
        `${API_ENDPOINTS.VARIANS}${mobilId ? `?mobil_id=${mobilId}` : ""}`
    );
    return useSWR<PaginatedResponse<Varian>>(url, fetcher);
};

export const useVarian = (id: string | number) => {
    const url = buildApiUrl(`${API_ENDPOINTS.VARIANS}/${id}`);
    return useSWR(id ? url : null, fetcher);
};

// Mutation hooks for creating data
export const createJanjiTemu = async (
    data: Omit<
        JanjiTemu,
        "id" | "created_at" | "updated_at" | "tanggal_request" | "status"
    >
) => {
    const url = buildApiUrl(API_ENDPOINTS.JANJI_TEMUS);
    return apiSafeFetch<JanjiTemu>(url, {
        method: "POST",
        body: JSON.stringify({
            ...data,
            status: "pending",
            tanggal_request: new Date().toISOString(),
        }),
    });
};

// Catalog hook - untuk menampilkan stok mobil yang tersedia
export const useCatalog = (params?: {
    page?: number;
    search?: string;
    brand?: string; // merek_id
    category?: string; // kategori_id
    mobil?: string; // mobil_id
    varian?: string; // varian_id
    minPrice?: number; // min_harga_jual
    maxPrice?: number; // max_harga_jual
    transmission?: string; // transmisi dari varian
    fuelType?: string; // jenis_bahan_bakar dari varian
    year?: string; // tahun
    condition?: "baru" | "bekas"; // kondisi
    sortBy?: string;
}) => {
    const filters: Record<string, string> = {
        status: "tersedia", // Hanya tampilkan yang tersedia
    };
    if (params?.search) filters.search = params.search;
    if (params?.brand) filters.merek_id = params.brand;
    if (params?.category) filters.kategori_id = params.category;
    if (params?.mobil) filters.mobil_id = params.mobil;
    if (params?.varian) filters.varian_id = params.varian;
    if (params?.minPrice) filters.min_harga_jual = params.minPrice.toString();
    if (params?.maxPrice && params.maxPrice < 1000000000)
        filters.max_harga_jual = params.maxPrice.toString();
    if (params?.year) filters.tahun = params.year;
    if (params?.condition) filters.kondisi = params.condition;

    // ✅ Map frontend sort values to backend-compatible values for StokMobil
    if (params?.sortBy) {
        const sortMapping: Record<string, string> = {
            newest: "-created_at", // newest first
            oldest: "created_at", // oldest first
            price_low: "harga_jual", // price low to high
            price_high: "-harga_jual", // price high to low
            year_newest: "-tahun", // newest year first
            year_oldest: "tahun", // oldest year first
        };

        filters.sort = sortMapping[params.sortBy] || params.sortBy;
    }

    if (params?.page) filters.page = params.page.toString(); // Debug log in development
    console.log("useCatalog called with params:", params);
    console.log("useCatalog filters built:", filters);

    return useStokMobilsByFilters(filters as any);
};

// Enhanced StokMobil hooks with filters
export const useStokMobilsByFilters = (filters?: {
    mobil_id?: number;
    varian_id?: number;
    kondisi?: "baru" | "bekas";
    status?: "tersedia" | "terjual" | "reserved";
    min_harga_jual?: number; // ✅ Fix parameter name
    max_harga_jual?: number; // ✅ Fix parameter name
    merek_id?: string; // ✅ Add missing parameter
    kategori_id?: string; // ✅ Add missing parameter
    search?: string; // ✅ Add missing parameter
    tahun?: string; // ✅ Add missing parameter
    sort?: string; // ✅ Add missing parameter
    page?: string; // ✅ Add missing parameter
    warna?: string;
}) => {
    const queryParams = filters
        ? new URLSearchParams(
              Object.entries(filters).reduce((acc, [key, value]) => {
                  if (value !== undefined && value !== "") {
                      acc[key] = value.toString();
                  }
                  return acc;
              }, {} as Record<string, string>)
          ).toString()
        : "";
    const url = buildApiUrl(
        `${API_ENDPOINTS.STOK_MOBILS}${queryParams ? `?${queryParams}` : ""}`
    ); // Debug log in development
    console.log("useStokMobilsByFilters URL:", url);
    console.log("useStokMobilsByFilters filters:", filters);
    console.log("useStokMobilsByFilters queryParams:", queryParams);

    return useSWR<PaginatedResponse<StokMobil>>(url, fetcher);
};

// Search and filter utilities
export const useCarSearch = (
    searchQuery: string,
    filters: CarSearchFilters = {}
) => {
    const allFilters = {
        ...filters,
        search: searchQuery,
    };

    return useMobils(allFilters);
};

export const useMobilsByMerek = (merekId: string | number) => {
    return useMobils({ merek_id: Number(merekId) });
};

export const useMobilsByKategori = (kategoriId: string | number) => {
    return useMobils({ kategori_id: Number(kategoriId) });
};

// Alias hooks for consistency
export const useBrands = () => {
    return useMereks();
};

export const useCategories = () => {
    return useKategoris();
};

export const useCars = (params?: {
    page?: number;
    search?: string;
    brand?: string;
    category?: string;
    mobil?: string; // filter berdasarkan mobil_id
    varian?: string; // filter berdasarkan varian_id
    minPrice?: number;
    maxPrice?: number;
    transmission?: string;
    fuelType?: string;
    year?: string;
    condition?: "baru" | "bekas";
    sortBy?: string;
}) => {
    // Gunakan useCatalog untuk menampilkan stok mobil yang tersedia
    return useCatalog(params);
};

// Utility functions for data processing
export const formatPrice = (price: number): string => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(price);
};

export const formatYear = (year: number): string => {
    return year.toString();
};

export const getCarSlug = (car: Mobil): string => {
    return `${car.nama?.toLowerCase().replace(/\s+/g, "-") || "unknown"}-${
        car.tahun_mulai || "unknown"
    }-${car.id}`;
};

export const getBrandSlug = (brand: Merek): string => {
    return `${brand.nama?.toLowerCase().replace(/\s+/g, "-") || "unknown"}-${
        brand.id
    }`;
};

export const getCategorySlug = (category: Kategori): string => {
    return `${category.nama?.toLowerCase().replace(/\s+/g, "-") || "unknown"}-${
        category.id
    }`;
};

// Sort options available for frontend use
export const SORT_OPTIONS = {
    NEWEST: "newest",
    OLDEST: "oldest",
    NAME_ASC: "name_asc",
    NAME_DESC: "name_desc",
    YEAR_NEWEST: "year_newest",
    YEAR_OLDEST: "year_oldest",
    PRICE_LOW: "price_low",
    PRICE_HIGH: "price_high",
} as const;

export type SortOption = (typeof SORT_OPTIONS)[keyof typeof SORT_OPTIONS];

// Helper to get valid sort options for UI
export const getSortOptions = () => [
    { value: SORT_OPTIONS.NEWEST, label: "Terbaru" },
    { value: SORT_OPTIONS.OLDEST, label: "Terlama" },
    { value: SORT_OPTIONS.NAME_ASC, label: "Nama A-Z" },
    { value: SORT_OPTIONS.NAME_DESC, label: "Nama Z-A" },
    { value: SORT_OPTIONS.YEAR_NEWEST, label: "Tahun Terbaru" },
    { value: SORT_OPTIONS.YEAR_OLDEST, label: "Tahun Terlama" },
];
