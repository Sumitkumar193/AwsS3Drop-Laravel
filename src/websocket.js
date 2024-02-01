import { WebSocketServer } from 'ws';
import SHA256 from "crypto-js/sha256.js";

export default function WebSocket(server) {
    const Clients = new Map();
    const wss = new WebSocketServer({ server });
    wss.on("connection", (ws, req) => {
        const id = uuidv4();
        const color = Math.floor(Math.random() * 360);
        const metadata = { id, color };

        const uniqueId = SHA256(JSON.stringify(metadata)).toString();

        Clients.set(uniqueId, ws);
        // console.log(`Connected Client ${uniqueId}`);
        console.log('Total Clients : ', Clients.size);


        ws.on("close", () => {
            // clients.delete(uniqueId);
            console.log(`Disconnected Client ${uniqueId}`);
        });

        ws.on('message', function incoming(message) {

            const { id, payload } = JSON.parse(message);

            const client = Clients.get(id);

            if (client && client.readyState === 1) {
                client.send(JSON.stringify(payload));
            }
        });

        ws.on("error", (ws, error) => {
            console.log(`Error ${uniqueId} : ${error}`);
            ws.send(JSON.stringify({ error }));
        });
    });

    function uuidv4() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
            var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }

}