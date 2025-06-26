import { Metadata } from "next";
import { Suspense } from "react";
import { getCatalogSEO } from "@/lib/seo";
import CatalogPage from "@/components/catalog/catalog-page";
import CatalogSkeleton from "@/components/catalog/catalog-skeleton";

export const metadata: Metadata = getCatalogSEO();

export default function MobilPage() {
    return (
        <div className="min-h-screen bg-gray-50 mt-16">
            <Suspense fallback={<CatalogSkeleton />}>
                <CatalogPage />
            </Suspense>
        </div>
    );
}
