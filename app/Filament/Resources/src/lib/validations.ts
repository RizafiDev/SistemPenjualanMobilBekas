import { z } from "zod";

// Car search filters schema
export const carSearchFiltersSchema = z.object({
    merek_id: z.number().optional(),
    kategori_id: z.number().optional(),
    harga_min: z.number().min(0).optional(),
    harga_max: z.number().min(0).optional(),
    tahun_min: z.number().min(1980).max(new Date().getFullYear()).optional(),
    tahun_max: z.number().min(1980).max(new Date().getFullYear()).optional(),
    transmisi: z.enum(["manual", "automatic"]).optional(),
    bahan_bakar: z.enum(["bensin", "diesel", "listrik", "hybrid"]).optional(),
    kondisi: z.enum(["baru", "bekas"]).optional(),
    search: z.string().optional(),
});

// Appointment form schema
export const appointmentFormSchema = z
    .object({
        nama_pelanggan: z
            .string()
            .min(2, "Nama minimal 2 karakter")
            .max(100, "Nama maksimal 100 karakter"),
        email_pelanggan: z.string().email("Format email tidak valid"),
        telepon_pelanggan: z
            .string()
            .min(10, "Nomor telepon minimal 10 digit")
            .max(15, "Nomor telepon maksimal 15 digit")
            .regex(/^[0-9+\-\s()]+$/, "Format nomor telepon tidak valid"),
        alamat_pelanggan: z
            .string()
            .max(500, "Alamat maksimal 500 karakter")
            .optional(),
        stok_mobil_id: z.number().min(1, "Pilih mobil yang diminati"),
        waktu_mulai: z.string().refine((date) => {
            const selectedDate = new Date(date);
            const now = new Date();
            return selectedDate > now;
        }, "Waktu janji temu harus di masa depan"),
        waktu_selesai: z.string(),
        jenis: z.enum(["test_drive", "konsultasi", "negosiasi"], {
            required_error: "Pilih jenis janji temu",
        }),
        tujuan: z
            .string()
            .max(1000, "Tujuan maksimal 1000 karakter")
            .optional(),
        pesan_tambahan: z
            .string()
            .max(1000, "Pesan tambahan maksimal 1000 karakter")
            .optional(),
        waktu_alternatif: z.string().optional(),
        metode: z.enum(["online", "offline"], {
            required_error: "Pilih metode janji temu",
        }),
        lokasi: z.enum(["showroom", "rumah_pelanggan"], {
            required_error: "Pilih lokasi janji temu",
        }),
    })
    .refine(
        (data) => {
            const waktuMulai = new Date(data.waktu_mulai);
            const waktuSelesai = new Date(data.waktu_selesai);
            return waktuSelesai > waktuMulai;
        },
        {
            message: "Waktu selesai harus setelah waktu mulai",
            path: ["waktu_selesai"],
        }
    );

// Appointment form validation schema
export const appointmentSchema = z.object({
    nama_pelanggan: z.string().min(2, "Nama minimal 2 karakter"),
    email_pelanggan: z.string().email("Email tidak valid"),
    telepon_pelanggan: z.string().min(10, "Nomor telepon minimal 10 digit"),
    alamat_pelanggan: z.string().optional(),
    stok_mobil_id: z.number().min(1, "Pilih varian mobil"),
    waktu_mulai: z.string().min(1, "Pilih waktu mulai"),
    waktu_selesai: z.string().min(1, "Pilih waktu selesai"),
    jenis: z.enum(["test_drive", "konsultasi", "negosiasi"]),
    tujuan: z.string().optional(),
    pesan_tambahan: z.string().optional(),
    waktu_alternatif: z.string().optional(),
    metode: z.enum(["online", "offline"]),
    lokasi: z.enum(["showroom", "rumah_pelanggan"]),
});

// Contact form schema
export const contactFormSchema = z.object({
    nama: z
        .string()
        .min(2, "Nama minimal 2 karakter")
        .max(100, "Nama maksimal 100 karakter"),
    email: z.string().email("Format email tidak valid"),
    telepon: z
        .string()
        .min(10, "Nomor telepon minimal 10 digit")
        .max(15, "Nomor telepon maksimal 15 digit")
        .regex(/^[0-9+\-\s()]+$/, "Format nomor telepon tidak valid"),
    subjek: z
        .string()
        .min(5, "Subjek minimal 5 karakter")
        .max(200, "Subjek maksimal 200 karakter"),
    pesan: z
        .string()
        .min(10, "Pesan minimal 10 karakter")
        .max(2000, "Pesan maksimal 2000 karakter"),
});

// Newsletter subscription schema
export const newsletterSchema = z.object({
    email: z.string().email("Format email tidak valid"),
});

// Car price range schema
export const priceRangeSchema = z
    .object({
        min: z.number().min(0, "Harga minimum tidak boleh negatif"),
        max: z.number().min(0, "Harga maksimum tidak boleh negatif"),
    })
    .refine((data) => data.max >= data.min, {
        message:
            "Harga maksimum harus lebih besar atau sama dengan harga minimum",
        path: ["max"],
    });

// Export types for use in components
export type CarSearchFilters = z.infer<typeof carSearchFiltersSchema>;
export type AppointmentForm = z.infer<typeof appointmentFormSchema>;
export type AppointmentFormData = z.infer<typeof appointmentSchema>;
export type ContactForm = z.infer<typeof contactFormSchema>;
export type NewsletterForm = z.infer<typeof newsletterSchema>;
export type PriceRange = z.infer<typeof priceRangeSchema>;
