import { Metadata } from "next";
import ContactPage from "@/components/contact/contact-page";

export const metadata: Metadata = {
    title: "Kontak Kami | Toko Jaya Motor",
    description:
        "Hubungi Toko Jaya Motor untuk informasi mobil bekas, konsultasi, atau layanan lainnya. Kami siap membantu Anda.",
    keywords: "kontak, hubungi kami, alamat, telepon, email, toko jaya motor",
};

export default function ContactPageRoute() {
    return <ContactPage />;
}
