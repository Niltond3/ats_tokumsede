import qz from "qz-tray";
import { QZ_SECURITY } from "@/config/qz-config";
// Substitua com seu certificado gerado
const CERTIFICATE = QZ_SECURITY.certificate;

const PRIVATE_KEY = QZ_SECURITY.privateKey;
export class PrintService {
    static async connect() {
        if (!qz.websocket.isActive()) {
            // Configura o certificado antes de conectar
            qz.security.setCertificatePromise(() => {
                return CERTIFICATE;
            });

            qz.security.setSignaturePromise((toSign) => {
                return qz.security.sign({
                    privateKey: PRIVATE_KEY,
                    input: toSign,
                });
            });

            await qz.websocket.connect();
        }
    }

    static async getPrinters() {
        await this.connect();
        return await qz.printers.find();
    }

    static async print(printerName, data) {
        await this.connect();
        const config = qz.configs.create(printerName);
        return await qz.print(config, data);
    }
}
