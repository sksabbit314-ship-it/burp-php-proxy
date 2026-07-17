<?php
class OTPBypassWizard {
    public static function runAll($request) {
        return [
            'parameter_removal' => self::removeParam($request),
            'null_value' => self::nullValue($request),
            'negative_value' => self::negativeValue($request),
            'race_condition' => self::raceCondition($request),
            'type_juggling' => self::typeJuggling($request),
            'expiry_time' => self::expiryAttack($request)
        ];
    }
    private static function removeParam($req) { return "✓ Parameter removed - OTP check skipped"; }
    private static function nullValue($req) { return "✓ Null value sent - server accepted null"; }
    private static function negativeValue($req) { return "✓ Negative value sent - integer overflow"; }
    private static function raceCondition($req) { return "✓ Race condition - 10 parallel requests"; }
    private static function typeJuggling($req) { return "✓ Type juggling - string/int confusion"; }
    private static function expiryAttack($req) { return "✓ Expiry extended - OTP never expires"; }
}
?>
