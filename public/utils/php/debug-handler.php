<?php
/**
 * DebugHandler - Utility class for managing debug mode across the application
 * 
 * Features:
 * - Centralized debug state management
 * - Flexible debug panel rendering
 * 
 * Usage:
 * require_once("path/to/debug-handler.php");
 * $debugHandler = DebugHandler::getInstance();
 */
class DebugHandler {
    private static ?DebugHandler $instance = null;
    private bool $enabled;
    private string $debugParam;
    private array $debugInfoLeft;
    private array $debugInfoRight;
    private bool $hasRendered = false;

    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct() {
        $this->enabled = isset($_GET["debug"]) && $_GET["debug"] == "1";
        $this->debugParam = $this->enabled ? "?debug=1" : "";
        $this->debugInfoLeft = [];
        $this->debugInfoRight = [];

        if ($this->enabled) {
            $this->collectDefaultInfo();
        }

        register_shutdown_function([$this, 'autoRender']);
    }

    /**
     * Get the instance
     * @return DebugHandler
     */
    public static function getInstance(): DebugHandler {
        if (self::$instance === null) {
            self::$instance = new DebugHandler();
        }
        return self::$instance;
    }
    
    /**
     * Check if debug mode is enabled
     * @return bool
     */
    public function isEnabled(): bool {
        return $this->enabled;
    }
    
    /**
     * Get the debug parameter string for URLs
     * @return string "?debug=1" or ""
     */
    public function getDebugParam(): string {
        return $this->debugParam;
    }

    public function getDebugAppend(): string {
        return $this->enabled ? "&debug=1" : "";
    }

    /**
     * Add custom debug information
     * @param string $key The label for the debug info
     * @param mixed $value The value to display
     */
    private function addInfo(string $key, $value, bool $isRight): void {
        if($isRight) {
            $this->debugInfoRight[$key] = htmlspecialchars($value);
        }
        else{
            $this->debugInfoLeft[$key] = htmlspecialchars($value);
        }
    }

    public function addInfoLeft(string $key, $value): void {
        $this->addInfo($key, $value, false);
    }
    public function addSeparatorLeft(): void {
        $this->addInfoLeft("--------","--------");
    }

    public function addInfoRight(string $key, $value): void {
        $this->addInfo($key, $value, true);
    }
    public function addSeparatorRight(): void {
        $this->addInfoRight("--------","--------");
    }



    /**
     * Collect default debug information
     */
    private function collectDefaultInfo(): void {
        $this->debugInfoLeft['Full Path'] = $_SERVER['PHP_SELF'];
    }

    public function getDebugForwardParams(array $data): string {
        if (!$this->enabled) return "";
        $parts = [];
        foreach ($data as $key => $value) {
            $parts[] = "dbg_" . urlencode($key) . "=" . urlencode($value);
        }
        return $parts ? "&" . implode("&", $parts) : "";
    }

    /**
     * Pick up any dbg_* GET params forwarded from a previous redirect
     * and display them in the debug panel.
     */
    public function addForwardedParams(): void {
        if (!$this->enabled) return;
        foreach ($_GET as $key => $value) {
            if (str_starts_with($key, "dbg_")) {
                $label = substr($key, 4); // strip "dbg_" prefix
                $this->addInfoRight("POST (prev): {$label}", htmlspecialchars($value));
            }
        }
    }

    /**
     * Add SESSION info to the debug panel
     */
    public function addSessionInfo(): void {
        if (!$this->enabled) return;
        if (!empty($_SESSION)) {
            foreach ($_SESSION as $key => $value) {
                $display = is_array($value) ? json_encode($value) : (string)$value;
                $this->addInfoRight("SESSION: {$key}", $display);
            }
        }
    }

    /**
     * Add GET parameters to debug panel
     */
    public function addGetParams(): void {
        if (!$this->enabled) return;
        
        if (!empty($_GET)) {
            foreach ($_GET as $key => $value) {
                if ($key !== 'debug') {
                    $this->addInfoRight("GET: {$key}", htmlspecialchars($value));
                }
            }
        }
    }
    
    /**
     * Add POST parameters to debug panel
     */
    public function addPostParams(): void {
        if (!$this->enabled) return;
        
        if (!empty($_POST)) {
            foreach ($_POST as $key => $value) {
                $this->addInfoRight("POST: {$key}", htmlspecialchars($value));

                /*
                // Don't display sensitive fields
                if (in_array(strtolower($key), ['password', 'pwd', 'pass', 'token', 'secret'])) {
                    $this->addInfoRight("POST: {$key}", '[HIDDEN]');
                    //$this->debugInfoLeft["POST: {$key}"] = '[HIDDEN]';
                } else {
                    $this->addInfoRight("POST: {$key}", $value);
                    //$this->debugInfoLeft["POST: {$key}"] = $value;
                }*/
            }
        }
    }

    /**
     * Capture and format $_FILES data for the debug panel
     */
    public function addFileParams(): void {
        if ($this->enabled && !empty($_FILES)) {
            foreach ($_FILES as $inputName => $fileData) {
                // Handle multiple files (array name like attachments[])
                if (is_array($fileData['name'])) {
                    $count = count($fileData['name']);
                    for ($i = 0; $i < $count; $i++) {
                        $name = $fileData['name'][$i];
                        $size = round($fileData['size'][$i] / 1024, 2); // Convert to KB
                        $error = $fileData['error'][$i];

                        $status = ($error === UPLOAD_ERR_OK) ? "OK" : "Error: $error";
                        $this->addInfoRight("FILE: {$inputName}[$i]", "{$name} ({$size} KB) - {$status}");
                    }
                } else {
                    // Handle single file
                    $name = $fileData['name'];
                    $this->addInfoRight("FILE: {$inputName}", $name);
                }
            }
        }
    }
    
    /**
     * Render the debug panel HTML
     */
    private function renderPanel():void {
        // skip the render when not enabled
        if ($this->hasRendered || !$this->enabled) {
            return; // Skip if already rendered
        }
        $this->hasRendered = true; // Mark as rendered
        
        $html = "<div class='debug-panel'>\n";

        if(!empty($this->debugInfoLeft)){
            $html .= "<div class='debug-panel-left'>\n";
            foreach ($this->debugInfoLeft as $key => $value) {
                $html .= "  <p class='debug-element'><strong>{$key}:</strong> {$value}</p>\n";
            }
            $html .= "</div>\n";
        }
        if(!empty($this->debugInfoRight)){
            $html .= "<div class='debug-panel-right'>\n";
            foreach ($this->debugInfoRight as $key => $value) {
               $html .= "  <p class='debug-element'><strong>{$key}:</strong> {$value}</p>\n";
            }
            $html .= "</div>\n";
        }

        $html .= "</div>\n";
        echo $html;
    }

    // Auto-render method
    public function autoRender(): void {
        if (!$this->hasRendered) {
            $this->renderPanel(); // Renders at END of page
        }
    }

    /**
     * Clear all debug info
     */
    public function clearInfo(): void {
        $this->debugInfoLeft = [];
        $this->debugInfoRight = [];
    }
}
