import { Metadata } from "next";
import { Suspense } from "react";
import AppointmentForm from "@/components/appointment/appointment-form";
import { Card, CardContent } from "@/components/ui/card";

export const metadata: Metadata = {
    title: "Buat Janji Temu | Toko Jaya Motor",
    description:
        "Buat janji temu untuk test drive atau konsultasi mobil bekas di Toko Jaya Motor. Layanan profesional dan terpercaya.",
    keywords: "janji temu, test drive, konsultasi mobil, appointment",
};

export default function AppointmentPage() {
    return (
        <div className="min-h-screen bg-gray-50 py-8">
            <div className="container mx-auto px-4">
                <div className="max-w-4xl mx-auto">
                    <div className="text-center mb-8">
                        <h1 className="text-3xl font-bold text-gray-900 mb-4">
                            Buat Janji Temu
                        </h1>
                        <p className="text-gray-600 max-w-2xl mx-auto">
                            Jadwalkan pertemuan dengan tim kami untuk test
                            drive, konsultasi, atau negosiasi harga. Kami siap
                            membantu Anda menemukan mobil yang tepat.
                        </p>
                    </div>

                    <Suspense fallback={<AppointmentSkeleton />}>
                        <AppointmentForm />
                    </Suspense>
                </div>
            </div>
        </div>
    );
}

function AppointmentSkeleton() {
    return (
        <Card>
            <CardContent className="p-8">
                <div className="space-y-6">
                    {Array.from({ length: 8 }).map((_, i) => (
                        <div key={i}>
                            <div className="h-4 w-24 bg-gray-200 rounded mb-2 animate-pulse"></div>
                            <div className="h-10 w-full bg-gray-200 rounded animate-pulse"></div>
                        </div>
                    ))}
                    <div className="h-12 w-full bg-gray-200 rounded animate-pulse"></div>
                </div>
            </CardContent>
        </Card>
    );
}
