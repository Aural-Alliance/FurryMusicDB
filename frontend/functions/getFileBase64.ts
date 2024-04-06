export default function (file: File | null): Promise<string|ArrayBuffer|null> {
    return new Promise((resolve, reject) => {
        if (file === null) {
            resolve(null);
        }

        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => resolve(reader.result);
        reader.onerror = reject;
    });
}
