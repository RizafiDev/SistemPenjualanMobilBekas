"use client";

import { useState, useEffect } from "react";
import { useSearchParams } from "next/navigation";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { Badge } from "@/components/ui/badge";
import { Slider } from "@/components/ui/slider";
import {
  useCars,
  useBrands,
  useCategories,
  useMobils,
  useVarians,
} from "@/lib/hooks";
import StockCarCard from "@/components/car/stock-car-card";
import { Filter, Search, X, ChevronLeft, ChevronRight } from "lucide-react";
import { toast } from "sonner";
import { formatCurrency } from "@/lib/utils";

interface CatalogFilters {
  search: string;
  brand: string;
  category: string;
  mobil: string; // filter berdasarkan mobil
  varian: string; // filter berdasarkan varian
  priceRange: [number, number];
  transmission: string;
  fuelType: string;
  year: string;
  condition: string; // tambah filter kondisi
  sortBy: string;
}

export default function CatalogPage() {
  const searchParams = useSearchParams();
  const [page, setPage] = useState(1);
  const [filters, setFilters] = useState<CatalogFilters>({
    search: searchParams.get("search") || "",
    brand: searchParams.get("brand") || "",
    category: searchParams.get("category") || "",
    mobil: searchParams.get("mobil") || "",
    varian: searchParams.get("varian") || "",
    priceRange: [0, 1000000000],
    transmission: "",
    fuelType: "",
    year: "",
    condition: "", // tambah kondisi filter
    sortBy: "newest",
  });
  const [showFilters, setShowFilters] = useState(false);
  const {
    data: stockCars, // ubah nama dari cars ke stockCars
    isLoading: carsLoading,
    error: carsError,
  } = useCars({
    page,
    search: filters.search,
    brand: filters.brand,
    category: filters.category,
    mobil: filters.mobil,
    varian: filters.varian,
    minPrice: filters.priceRange[0],
    maxPrice: filters.priceRange[1],
    transmission: filters.transmission,
    fuelType: filters.fuelType,
    year: filters.year,
    condition: filters.condition as "baru" | "bekas",
    sortBy: filters.sortBy,
  });

  const { data: brands, isLoading: brandsLoading } = useBrands();
  const { data: categories, isLoading: categoriesLoading } = useCategories();
  const { data: mobils, isLoading: mobilsLoading } = useMobils();
  const { data: varians, isLoading: variansLoading } = useVarians();

  // Debug logging
  useEffect(() => {
    console.log("CatalogPage - stockCars data:", stockCars);
    console.log("CatalogPage - carsLoading:", carsLoading);
    console.log("CatalogPage - carsError:", carsError);
    console.log("CatalogPage - filters:", filters);
    console.log("CatalogPage - page:", page);
  }, [stockCars, carsLoading, carsError, filters, page]);

  useEffect(() => {
    if (carsError) {
      toast.error("Gagal memuat data mobil. Silakan coba lagi.");
    }
  }, [carsError]);
  const handleFilterChange = (key: keyof CatalogFilters, value: any) => {
    const processedValue = value === "all" ? "" : value;
    setFilters((prev) => ({ ...prev, [key]: processedValue }));
    setPage(1); // Reset to first page when filters change
  };
  const clearFilters = () => {
    setFilters({
      search: "",
      brand: "",
      category: "",
      mobil: "",
      varian: "",
      priceRange: [0, 1000000000],
      transmission: "",
      fuelType: "",
      year: "",
      condition: "",
      sortBy: "newest",
    });
    setPage(1);
  };

  const activeFiltersCount = Object.entries(filters).filter(([key, value]) => {
    if (key === "priceRange") return value[0] > 0 || value[1] < 1000000000;
    if (key === "sortBy") return false;
    return value !== "";
  }).length;

  const totalPages = stockCars?.last_page || 1;

  return (
    <div className="container mx-auto px-4 py-8">
      {/* Header */}
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-gray-900 mb-2">
          Katalog Mobil Bekas
        </h1>
        <p className="text-gray-600">
          Temukan mobil bekas berkualitas dengan berbagai pilihan merek dan
          kategori
        </p>
      </div>{" "}
      <div className="flex flex-col lg:flex-row gap-8">
        {/* Filters Sidebar - Made Sticky */}
        <div className="lg:w-1/4">
          {/* Mobile Filter Toggle */}
          <Button
            variant="outline"
            className="w-full mb-4 lg:hidden"
            onClick={() => setShowFilters(!showFilters)}
          >
            <Filter className="w-4 h-4 mr-2" />
            Filter ({activeFiltersCount})
          </Button>

          {/* Sticky Filter Card */}
          <div className="sticky top-20 z-10">
            <Card
              className={`${
                showFilters ? "block" : "hidden"
              } lg:block shadow-xl border-0 bg-white`}
            >
              <CardHeader>
                <div className="flex items-center justify-between">
                  <CardTitle className="text-lg font-semibold flex items-center gap-2">
                    <Filter className="w-5 h-5 text-blue-600" />
                    Filter
                  </CardTitle>
                  {activeFiltersCount > 0 && (
                    <Button
                      variant="ghost"
                      size="sm"
                      onClick={clearFilters}
                      className="text-red-500 hover:text-red-700 hover:bg-red-50"
                    >
                      <X className="w-4 h-4 mr-1" />
                      Reset
                    </Button>
                  )}
                </div>
              </CardHeader>
              <CardContent className="space-y-6 max-h-[calc(100vh-12rem)] overflow-y-auto custom-scrollbar">
                {/* Search */}
                <div>
                  <label className="text-sm font-medium mb-2 block">
                    Pencarian
                  </label>{" "}
                  <div className="relative">
                    <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-4 h-4" />
                    <Input
                      placeholder="Cari mobil..."
                      value={filters.search}
                      onChange={(e) =>
                        handleFilterChange("search", e.target.value)
                      }
                      className="pl-10 h-12 border-2 focus:border-blue-500 rounded-xl"
                    />
                  </div>
                </div>
                {/* Brand */}
                <div>
                  <label className="text-sm font-medium mb-2 block">
                    Merek
                  </label>{" "}
                  <Select
                    value={filters.brand}
                    onValueChange={(value) =>
                      handleFilterChange("brand", value)
                    }
                  >
                    <SelectTrigger className="h-12 border-2 focus:border-blue-500 rounded-xl">
                      <SelectValue placeholder="Pilih merek" />
                    </SelectTrigger>{" "}
                    <SelectContent>
                      {" "}
                      <SelectItem value="all">Semua Merek</SelectItem>{" "}
                      {Array.isArray(brands?.data) &&
                        brands.data.map((brand: any) => (
                          <SelectItem
                            key={brand.id}
                            value={brand.id.toString()}
                          >
                            {brand.nama}
                          </SelectItem>
                        ))}
                    </SelectContent>
                  </Select>
                </div>{" "}
                {/* Category */}
                <div>
                  <label className="text-sm font-medium mb-2 block">
                    Kategori
                  </label>
                  <Select
                    value={filters.category}
                    onValueChange={(value) =>
                      handleFilterChange("category", value)
                    }
                  >
                    <SelectTrigger className="h-12 border-2 focus:border-blue-500 rounded-xl">
                      <SelectValue placeholder="Pilih kategori" />
                    </SelectTrigger>{" "}
                    <SelectContent>
                      <SelectItem value="all">Semua Kategori</SelectItem>
                      {Array.isArray(categories?.data) &&
                        categories.data.map((category: any) => (
                          <SelectItem
                            key={category.id}
                            value={category.id.toString()}
                          >
                            {category.nama}
                          </SelectItem>
                        ))}
                    </SelectContent>
                  </Select>
                </div>{" "}
                {/* Mobil Filter */}
                <div>
                  <label className="text-sm font-medium mb-2 block">
                    Mobil
                  </label>
                  <Select
                    value={filters.mobil}
                    onValueChange={(value) =>
                      handleFilterChange("mobil", value)
                    }
                  >
                    <SelectTrigger className="h-12 border-2 focus:border-blue-500 rounded-xl">
                      <SelectValue placeholder="Pilih mobil" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="all">Semua Mobil</SelectItem>
                      {Array.isArray(mobils?.data) &&
                        mobils.data.map((mobil: any) => (
                          <SelectItem
                            key={mobil.id}
                            value={mobil.id.toString()}
                          >
                            {mobil.nama}
                          </SelectItem>
                        ))}
                    </SelectContent>
                  </Select>
                </div>{" "}
                {/* Varian Filter */}
                <div>
                  <label className="text-sm font-medium mb-2 block">
                    Varian
                  </label>
                  <Select
                    value={filters.varian}
                    onValueChange={(value) =>
                      handleFilterChange("varian", value)
                    }
                  >
                    <SelectTrigger className="h-12 border-2 focus:border-blue-500 rounded-xl">
                      <SelectValue placeholder="Pilih varian" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="all">Semua Varian</SelectItem>
                      {Array.isArray(varians?.data) &&
                        varians.data
                          .filter((varian: any) => {
                            // Filter varian berdasarkan mobil yang dipilih
                            if (!filters.mobil || filters.mobil === "all") {
                              return true;
                            }
                            return varian.mobil_id.toString() === filters.mobil;
                          })
                          .map((varian: any) => (
                            <SelectItem
                              key={varian.id}
                              value={varian.id.toString()}
                            >
                              {varian.nama}
                            </SelectItem>
                          ))}
                    </SelectContent>
                  </Select>
                </div>
                {/* Price Range */}
                <div>
                  <label className="text-sm font-medium mb-2 block">
                    Harga: {formatCurrency(filters.priceRange[0])} -{" "}
                    {formatCurrency(filters.priceRange[1])}
                  </label>
                  <Slider
                    value={filters.priceRange}
                    onValueChange={(value) =>
                      handleFilterChange("priceRange", value)
                    }
                    min={0}
                    max={1000000000}
                    step={10000000}
                    className="w-full"
                  />
                </div>{" "}
                {/* Transmission */}
                <div>
                  <label className="text-sm font-medium mb-2 block">
                    Transmisi
                  </label>
                  <Select
                    value={filters.transmission}
                    onValueChange={(value) =>
                      handleFilterChange("transmission", value)
                    }
                  >
                    <SelectTrigger className="h-12 border-2 focus:border-blue-500 rounded-xl">
                      <SelectValue placeholder="Pilih transmisi" />
                    </SelectTrigger>{" "}
                    <SelectContent>
                      <SelectItem value="all">Semua Transmisi</SelectItem>
                      <SelectItem value="Manual">Manual</SelectItem>
                      <SelectItem value="Automatic">Automatic</SelectItem>
                      <SelectItem value="CVT">CVT</SelectItem>
                    </SelectContent>
                  </Select>
                </div>
                {/* Fuel Type */}
                <div>
                  <label className="text-sm font-medium mb-2 block">
                    Bahan Bakar
                  </label>{" "}
                  <Select
                    value={filters.fuelType}
                    onValueChange={(value) =>
                      handleFilterChange("fuelType", value)
                    }
                  >
                    <SelectTrigger className="h-12 border-2 focus:border-blue-500 rounded-xl">
                      <SelectValue placeholder="Pilih bahan bakar" />
                    </SelectTrigger>{" "}
                    <SelectContent>
                      <SelectItem value="all">Semua Bahan Bakar</SelectItem>
                      <SelectItem value="Bensin">Bensin</SelectItem>
                      <SelectItem value="Diesel">Diesel</SelectItem>
                      <SelectItem value="Hybrid">Hybrid</SelectItem>
                      <SelectItem value="Electric">Electric</SelectItem>
                    </SelectContent>
                  </Select>
                </div>{" "}
                {/* Condition */}
                <div>
                  <label className="text-sm font-medium mb-2 block">
                    Kondisi
                  </label>
                  <Select
                    value={filters.condition}
                    onValueChange={(value) =>
                      handleFilterChange("condition", value)
                    }
                  >
                    <SelectTrigger className="h-12 border-2 focus:border-blue-500 rounded-xl">
                      <SelectValue placeholder="Pilih kondisi" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="all">Semua Kondisi</SelectItem>
                      <SelectItem value="baru">Baru</SelectItem>
                      <SelectItem value="bekas">Bekas</SelectItem>
                    </SelectContent>
                  </Select>
                </div>{" "}
                {/* Year */}
                <div>
                  <label className="text-sm font-medium mb-2 block">
                    Tahun
                  </label>
                  <Select
                    value={filters.year}
                    onValueChange={(value) => handleFilterChange("year", value)}
                  >
                    <SelectTrigger className="h-12 border-2 focus:border-blue-500 rounded-xl">
                      <SelectValue placeholder="Pilih tahun" />
                    </SelectTrigger>{" "}
                    <SelectContent>
                      <SelectItem value="all">Semua Tahun</SelectItem>
                      {Array.from({ length: 25 }, (_, i) => {
                        const year = new Date().getFullYear() - i;
                        return (
                          <SelectItem key={year} value={year.toString()}>
                            {year}
                          </SelectItem>
                        );
                      })}
                    </SelectContent>
                  </Select>{" "}
                </div>
              </CardContent>
            </Card>
          </div>
        </div>

        {/* Main Content */}
        <div className="lg:w-3/4">
          {/* Sort and Results Info */}
          <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            {" "}
            <div className="flex items-center gap-4">
              {" "}
              <p className="text-gray-600">
                {stockCars?.data?.length || 0} mobil ditemukan
              </p>
              {activeFiltersCount > 0 && (
                <Badge variant="secondary">
                  {activeFiltersCount} filter aktif
                </Badge>
              )}
            </div>{" "}
            <Select
              value={filters.sortBy}
              onValueChange={(value) => handleFilterChange("sortBy", value)}
            >
              <SelectTrigger className="w-48 h-12 border-2 focus:border-blue-500 rounded-xl">
                <SelectValue placeholder="Urutkan" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="newest">Terbaru</SelectItem>
                <SelectItem value="oldest">Terlama</SelectItem>
                <SelectItem value="price_low">Harga Terendah</SelectItem>
                <SelectItem value="price_high">Harga Tertinggi</SelectItem>
                <SelectItem value="year_new">Tahun Terbaru</SelectItem>
                <SelectItem value="year_old">Tahun Terlama</SelectItem>
              </SelectContent>
            </Select>
          </div>

          {/* Cars Grid */}
          {carsLoading ? (
            <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
              {Array.from({ length: 6 }).map((_, i) => (
                <Card key={i} className="animate-pulse">
                  <div className="h-48 bg-gray-200 rounded-t-lg"></div>
                  <CardContent className="p-4">
                    <div className="h-4 bg-gray-200 rounded mb-2"></div>
                    <div className="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                    <div className="h-6 bg-gray-200 rounded w-1/2"></div>
                  </CardContent>
                </Card>
              ))}
            </div>
          ) : stockCars?.data?.length ? (
            <>
              <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                {stockCars.data.map((stockCar: any) => (
                  <StockCarCard key={stockCar.id} stockCar={stockCar} />
                ))}
              </div>

              {/* Pagination */}
              {totalPages > 1 && (
                <div className="flex justify-center items-center mt-8 gap-2">
                  <Button
                    variant="outline"
                    size="sm"
                    onClick={() => setPage(page - 1)}
                    disabled={page === 1}
                  >
                    <ChevronLeft className="w-4 h-4" />
                    Sebelumnya
                  </Button>

                  <div className="flex items-center gap-1">
                    {Array.from({ length: Math.min(5, totalPages) }, (_, i) => {
                      let pageNum;
                      if (totalPages <= 5) {
                        pageNum = i + 1;
                      } else if (page <= 3) {
                        pageNum = i + 1;
                      } else if (page >= totalPages - 2) {
                        pageNum = totalPages - 4 + i;
                      } else {
                        pageNum = page - 2 + i;
                      }

                      return (
                        <Button
                          key={pageNum}
                          variant={page === pageNum ? "default" : "outline"}
                          size="sm"
                          onClick={() => setPage(pageNum)}
                        >
                          {pageNum}
                        </Button>
                      );
                    })}
                  </div>

                  <Button
                    variant="outline"
                    size="sm"
                    onClick={() => setPage(page + 1)}
                    disabled={page === totalPages}
                  >
                    Selanjutnya
                    <ChevronRight className="w-4 h-4" />
                  </Button>
                </div>
              )}
            </>
          ) : (
            <Card>
              <CardContent className="py-16 text-center">
                <div className="text-gray-400 mb-4">
                  <Search className="w-16 h-16 mx-auto" />
                </div>
                <h3 className="text-lg font-semibold text-gray-900 mb-2">
                  Mobil tidak ditemukan
                </h3>
                <p className="text-gray-600 mb-4">
                  Coba ubah filter pencarian Anda untuk melihat hasil yang
                  berbeda.
                </p>
                <Button onClick={clearFilters}>Reset Filter</Button>
              </CardContent>
            </Card>
          )}
        </div>
      </div>
    </div>
  );
}
