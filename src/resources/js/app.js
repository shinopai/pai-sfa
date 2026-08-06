import Alpine from "alpinejs";

window.Alpine = Alpine;

Alpine.start();

// ダッシュボード
import "./dashboard";

// Viteにimagesフォルダ内の全ファイルをアセットとして認識させる
import.meta.glob(["../images/**"]);
