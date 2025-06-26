"use client";

import { useState } from "react";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { Button } from "@/components/ui/button";
import {
    Form,
    FormControl,
    FormField,
    FormItem,
    FormLabel,
} from "@/components/ui/form";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import { Input } from "@/components/ui/input";
import { Search, Filter } from "lucide-react";
import { carSearchFiltersSchema } from "@/lib/validations";
import { useMereks, useKategoris } from "@/lib/hooks";
import type { CarSearchFilters } from "@/lib/validations";

interface CarSearchProps {
    onSearch: (filters: CarSearchFilters) => void;
    initialFilters?: Partial<CarSearchFilters>;
    showAdvancedFilters?: boolean;
    className?: string;
}

export default function CarSearch({
    onSearch,
    initialFilters = {},
    showAdvancedFilters = false,
    className,
}: CarSearchProps) {
    const [showAdvanced, setShowAdvanced] = useState(showAdvancedFilters);
    const { data: mereksData } = useMereks();
    const { data: kategorisData } = useKategoris();
    const mereks = Array.isArray(mereksData?.data) ? mereksData.data : [];
    const kategoris = Array.isArray(kategorisData?.data)
        ? kategorisData.data
        : [];

    const form = useForm<CarSearchFilters>({
        resolver: zodResolver(carSearchFiltersSchema),
        defaultValues: {
            merek_id: initialFilters.merek_id,
            kategori_id: initialFilters.kategori_id,
            harga_min: initialFilters.harga_min,
            harga_max: initialFilters.harga_max,
            tahun_min: initialFilters.tahun_min,
            tahun_max: initialFilters.tahun_max,
            transmisi: initialFilters.transmisi,
            bahan_bakar: initialFilters.bahan_bakar,
            kondisi: initialFilters.kondisi,
            search: initialFilters.search || "",
        },
    });

    const currentYear = new Date().getFullYear();
    const years = Array.from(
        { length: currentYear - 1980 + 1 },
        (_, i) => currentYear - i
    );

    const priceRanges = [
        { value: [0, 50000000], label: "Di bawah 50 juta" },
        { value: [50000000, 100000000], label: "50 - 100 juta" },
        { value: [100000000, 200000000], label: "100 - 200 juta" },
        { value: [200000000, 500000000], label: "200 - 500 juta" },
        { value: [500000000, 999999999999], label: "Di atas 500 juta" },
    ];

    const onSubmit = (data: CarSearchFilters) => {
        // Remove empty values
        const cleanData = Object.fromEntries(
            Object.entries(data).filter(
                ([_, value]) => value !== undefined && value !== ""
            )
        );
        onSearch(cleanData as CarSearchFilters);
    };

    const resetFilters = () => {
        form.reset({
            search: "",
        });
        onSearch({ search: "" });
    };    return (
        <div className={`bg-white rounded-2xl shadow-xl border border-gray-100 p-8 ${className}`}>
            <Form {...form}>
                <form
                    onSubmit={form.handleSubmit(onSubmit)}
                    className="space-y-6"
                >
                    {/* Search Input */}
                    <FormField
                        control={form.control}
                        name="search"
                        render={({ field }) => (
                            <FormItem>
                                <FormLabel className="text-lg font-semibold text-gray-900">Cari Mobil</FormLabel>
                                <FormControl>
                                    <div className="relative">
                                        <Search className="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 h-5 w-5" />
                                        <Input
                                            placeholder="Nama mobil, merek, atau kata kunci..."
                                            className="pl-12 h-12 text-base border-2 focus:border-blue-500 rounded-xl"
                                            {...field}
                                        />
                                    </div>
                                </FormControl>
                            </FormItem>
                        )}
                    />                    {/* Basic Filters Row */}
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <FormField
                            control={form.control}
                            name="merek_id"
                            render={({ field }) => (
                                <FormItem>
                                    <FormLabel className="text-base font-semibold text-gray-900">Merek</FormLabel>
                                    <Select
                                        onValueChange={(value) =>
                                            field.onChange(
                                                value && value !== "all"
                                                    ? Number(value)
                                                    : undefined
                                            )
                                        }
                                        value={field.value?.toString()}
                                    >
                                        <FormControl>
                                            <SelectTrigger className="h-12 border-2 focus:border-blue-500 rounded-xl">
                                                <SelectValue placeholder="Pilih merek" />
                                            </SelectTrigger>
                                        </FormControl>{" "}
                                        <SelectContent>
                                            <SelectItem value="all">
                                                Semua Merek
                                            </SelectItem>
                                            {mereks.map((merek) => (
                                                <SelectItem
                                                    key={merek.id}
                                                    value={merek.id.toString()}
                                                >
                                                    {merek.nama}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                </FormItem>
                            )}
                        />

                        <FormField
                            control={form.control}
                            name="kategori_id"
                            render={({ field }) => (
                                <FormItem>
                                    <FormLabel>Kategori</FormLabel>
                                    <Select
                                        onValueChange={(value) =>
                                            field.onChange(
                                                value && value !== "all"
                                                    ? Number(value)
                                                    : undefined
                                            )
                                        }
                                        value={field.value?.toString()}
                                    >
                                        <FormControl>
                                            <SelectTrigger>
                                                <SelectValue placeholder="Pilih kategori" />
                                            </SelectTrigger>
                                        </FormControl>{" "}
                                        <SelectContent>
                                            <SelectItem value="all">
                                                Semua Kategori
                                            </SelectItem>
                                            {kategoris.map((kategori) => (
                                                <SelectItem
                                                    key={kategori.id}
                                                    value={kategori.id.toString()}
                                                >
                                                    {kategori.nama}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                </FormItem>
                            )}
                        />

                        <FormField
                            control={form.control}
                            name="tahun_min"
                            render={({ field }) => (
                                <FormItem>
                                    <FormLabel>Tahun Minimum</FormLabel>
                                    <Select
                                        onValueChange={(value) =>
                                            field.onChange(
                                                value && value !== "all"
                                                    ? Number(value)
                                                    : undefined
                                            )
                                        }
                                        value={field.value?.toString()}
                                    >
                                        {" "}
                                        <FormControl>
                                            <SelectTrigger>
                                                <SelectValue placeholder="Tahun min" />
                                            </SelectTrigger>
                                        </FormControl>
                                        <SelectContent>
                                            <SelectItem value="all">
                                                Semua Tahun
                                            </SelectItem>
                                            {years.map((year) => (
                                                <SelectItem
                                                    key={year}
                                                    value={year.toString()}
                                                >
                                                    {year}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                    </Select>
                                </FormItem>
                            )}
                        />
                    </div>

                    {/* Advanced Filters Toggle */}
                    <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        onClick={() => setShowAdvanced(!showAdvanced)}
                        className="w-full md:w-auto"
                    >
                        <Filter className="mr-2 h-4 w-4" />
                        {showAdvanced ? "Sembunyikan" : "Tampilkan"} Filter
                        Lanjutan
                    </Button>

                    {/* Advanced Filters */}
                    {showAdvanced && (
                        <div className="space-y-4 p-4 bg-gray-50 rounded-lg">
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <FormField
                                    control={form.control}
                                    name="tahun_max"
                                    render={({ field }) => (
                                        <FormItem>
                                            <FormLabel>
                                                Tahun Maksimum
                                            </FormLabel>{" "}
                                            <Select
                                                onValueChange={(value) =>
                                                    field.onChange(
                                                        value && value !== "all"
                                                            ? Number(value)
                                                            : undefined
                                                    )
                                                }
                                                value={field.value?.toString()}
                                            >
                                                <FormControl>
                                                    <SelectTrigger>
                                                        <SelectValue placeholder="Tahun max" />
                                                    </SelectTrigger>
                                                </FormControl>{" "}
                                                <SelectContent>
                                                    <SelectItem value="all">
                                                        Semua Tahun
                                                    </SelectItem>
                                                    {years.map((year) => (
                                                        <SelectItem
                                                            key={year}
                                                            value={year.toString()}
                                                        >
                                                            {year}
                                                        </SelectItem>
                                                    ))}
                                                </SelectContent>
                                            </Select>
                                        </FormItem>
                                    )}
                                />

                                <FormField
                                    control={form.control}
                                    name="transmisi"
                                    render={({ field }) => (
                                        <FormItem>
                                            {" "}
                                            <FormLabel>Transmisi</FormLabel>
                                            <Select
                                                onValueChange={(value) =>
                                                    field.onChange(
                                                        value === "all"
                                                            ? undefined
                                                            : value
                                                    )
                                                }
                                                value={field.value}
                                            >
                                                <FormControl>
                                                    <SelectTrigger>
                                                        <SelectValue placeholder="Pilih transmisi" />
                                                    </SelectTrigger>
                                                </FormControl>{" "}
                                                <SelectContent>
                                                    <SelectItem value="all">
                                                        Semua Transmisi
                                                    </SelectItem>
                                                    <SelectItem value="manual">
                                                        Manual
                                                    </SelectItem>
                                                    <SelectItem value="automatic">
                                                        Automatic
                                                    </SelectItem>
                                                </SelectContent>
                                            </Select>
                                        </FormItem>
                                    )}
                                />

                                <FormField
                                    control={form.control}
                                    name="bahan_bakar"
                                    render={({ field }) => (
                                        <FormItem>
                                            {" "}
                                            <FormLabel>Bahan Bakar</FormLabel>
                                            <Select
                                                onValueChange={(value) =>
                                                    field.onChange(
                                                        value === "all"
                                                            ? undefined
                                                            : value
                                                    )
                                                }
                                                value={field.value}
                                            >
                                                <FormControl>
                                                    <SelectTrigger>
                                                        <SelectValue placeholder="Pilih bahan bakar" />
                                                    </SelectTrigger>
                                                </FormControl>{" "}
                                                <SelectContent>
                                                    <SelectItem value="all">
                                                        Semua Bahan Bakar
                                                    </SelectItem>
                                                    <SelectItem value="bensin">
                                                        Bensin
                                                    </SelectItem>
                                                    <SelectItem value="diesel">
                                                        Diesel
                                                    </SelectItem>
                                                    <SelectItem value="listrik">
                                                        Listrik
                                                    </SelectItem>
                                                    <SelectItem value="hybrid">
                                                        Hybrid
                                                    </SelectItem>
                                                </SelectContent>
                                            </Select>
                                        </FormItem>
                                    )}
                                />

                                <FormField
                                    control={form.control}
                                    name="kondisi"
                                    render={({ field }) => (
                                        <FormItem>
                                            {" "}
                                            <FormLabel>Kondisi</FormLabel>
                                            <Select
                                                onValueChange={(value) =>
                                                    field.onChange(
                                                        value === "all"
                                                            ? undefined
                                                            : value
                                                    )
                                                }
                                                value={field.value}
                                            >
                                                <FormControl>
                                                    <SelectTrigger>
                                                        <SelectValue placeholder="Pilih kondisi" />
                                                    </SelectTrigger>
                                                </FormControl>{" "}
                                                <SelectContent>
                                                    <SelectItem value="all">
                                                        Semua Kondisi
                                                    </SelectItem>
                                                    <SelectItem value="baru">
                                                        Baru
                                                    </SelectItem>
                                                    <SelectItem value="bekas">
                                                        Bekas
                                                    </SelectItem>
                                                </SelectContent>
                                            </Select>
                                        </FormItem>
                                    )}
                                />

                                <FormField
                                    control={form.control}
                                    name="harga_min"
                                    render={({ field }) => (
                                        <FormItem>
                                            <FormLabel>Harga Minimum</FormLabel>
                                            <FormControl>
                                                <Input
                                                    type="number"
                                                    placeholder="0"
                                                    {...field}
                                                    onChange={(e) =>
                                                        field.onChange(
                                                            e.target.value
                                                                ? Number(
                                                                      e.target
                                                                          .value
                                                                  )
                                                                : undefined
                                                        )
                                                    }
                                                />
                                            </FormControl>
                                        </FormItem>
                                    )}
                                />

                                <FormField
                                    control={form.control}
                                    name="harga_max"
                                    render={({ field }) => (
                                        <FormItem>
                                            <FormLabel>
                                                Harga Maksimum
                                            </FormLabel>
                                            <FormControl>
                                                <Input
                                                    type="number"
                                                    placeholder="999999999"
                                                    {...field}
                                                    onChange={(e) =>
                                                        field.onChange(
                                                            e.target.value
                                                                ? Number(
                                                                      e.target
                                                                          .value
                                                                  )
                                                                : undefined
                                                        )
                                                    }
                                                />
                                            </FormControl>
                                        </FormItem>
                                    )}
                                />
                            </div>

                            {/* Quick Price Ranges */}
                            <div>
                                <FormLabel className="text-sm font-medium">
                                    Range Harga Populer
                                </FormLabel>
                                <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-2 mt-2">
                                    {priceRanges.map((range, index) => (
                                        <Button
                                            key={index}
                                            type="button"
                                            variant="outline"
                                            size="sm"
                                            onClick={() => {
                                                form.setValue(
                                                    "harga_min",
                                                    range.value[0]
                                                );
                                                form.setValue(
                                                    "harga_max",
                                                    range.value[1] ===
                                                        999999999999
                                                        ? undefined
                                                        : range.value[1]
                                                );
                                            }}
                                            className="text-xs"
                                        >
                                            {range.label}
                                        </Button>
                                    ))}
                                </div>
                            </div>
                        </div>
                    )}

                    {/* Action Buttons */}
                    <div className="flex flex-col sm:flex-row gap-2">
                        <Button type="submit" className="flex-1">
                            <Search className="mr-2 h-4 w-4" />
                            Cari Mobil
                        </Button>
                        <Button
                            type="button"
                            variant="outline"
                            onClick={resetFilters}
                        >
                            Reset Filter
                        </Button>
                    </div>
                </form>
            </Form>
        </div>
    );
}
