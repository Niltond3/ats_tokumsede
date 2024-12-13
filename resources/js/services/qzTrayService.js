import { ref } from "vue";
import qz from "qz-tray";
import { QZ_SECURITY } from "@/config/qz-config";

const connected = ref(null);

const setCertificatePromise = async () => {
    await qz.security.setCertificatePromise(function (resolve, reject) {
        resolve(QZ_SECURITY.certificate);
    });

    qz.security.setSignatureAlgorithm("SHA512"); // Since 2.1
    await qz.security.setSignaturePromise(function (toSign) {
        return function (resolve, reject) {
            try {
                var pk = KEYUTIL.getKey(QZ_SECURITY.privateKey);
                var sig = new KJUR.crypto.Signature({ alg: "SHA512withRSA" }); // Use "SHA1withRSA" for QZ Tray 2.0 and older
                sig.init(pk);
                sig.updateString(toSign);
                var hex = sig.sign();
                resolve(stob64(hextorstr(hex)));
            } catch (err) {
                reject(err);
            }
        };
    });
};
const connectToQZ = async () => {
    try {
        // Tenta se conectar ao QZ Tray
        await qz.websocket.connect();
        // Se a conexão for bem-sucedida
        connected.value = true;
    } catch (error) {
        // Se ocorrer algum erro, suprimimos qualquer mensagem
        connected.value = false;
    }
};

export const qzTrayService = {
    async connect() {
        await setCertificatePromise();
        await connectToQZ();
        if (!connected.value) {
            return;
        }
        return true;
    },

    async listPrinters() {
        const printers = await qz.printers.find();
        return printers;
    },
    async findPrinter(printerName) {
        const printer = await qz.printers.find(printerName);
        return printer;
    },
    async print(printer, data) {
        const config = qz.configs.create(printer);
        await qz.print(config, data);
        return true;
    },
    async checkConnection() {},
};
