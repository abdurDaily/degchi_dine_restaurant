<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NoWebsiteLinks implements ValidationRule
{
    /**
     * Common top-level domains used by real websites and URL shorteners.
     * Keeping an explicit list avoids rejecting ordinary addresses such as
     * "Flat 4A, St. John Road, Dhaka" while still catching the common
     * link formats used in spam submissions.
     */
    private const TLD_PATTERN = 'com|net|org|edu|gov|mil|int|info|biz|io|co|ai|app|dev|ly|me|tv|xyz|online|site|tech|store|shop|blog|cloud|link|click|country|stream|download|ws|to|in|bd|uk|us|ca|au|nz|de|fr|it|es|nl|se|no|dk|fi|pl|ru|ua|tr|ir|pk|cn|jp|kr|sg|my|th|vn|id|ph|br|mx|ar|cl|za|ng|ke|eg|ae|sa|il|ch|at|be|eu';

    /**
     * Reject web links, IP URLs, HTML tags and script-like payloads in an
     * address. The rule is deliberately boundary-aware so punctuation,
     * apartment numbers and postal codes remain valid.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            return;
        }

        $address = trim($value);

        if ($address === '') {
            return;
        }

        $patterns = [
            // Raw or entity-encoded HTML tags.
            '/<[^>]*>/u',
            '/&(?:lt|gt|#0*60|#x0*3c|#0*62|#x0*3e);/iu',

            // URL schemes, protocol-relative URLs and script/data schemes.
            '~(?:(?:https?|ftps?|sftp)://|//|javascript:|data:|vbscript:|file:|mailto:)~iu',

            // www.example.com, sub.example.com, example.com, bit.ly/x, etc.
            '/(?:^|[\s(,;\[])(?:www\.)[a-z0-9](?:[a-z0-9-]*[a-z0-9])?(?:\.[a-z0-9](?:[a-z0-9-]*[a-z0-9])?)+(?=$|[\/:?#.,;)\],])/iu',
            '/(?:^|[\s(,;\[])(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+(?:'.self::TLD_PATTERN.')(?=$|[\/:?#.,;)\],])/iu',

            // IPv4 URLs, including an optional port or path.
            '/(?:^|[\s(,;\[])(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(?:\.(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}(?::\d{1,5})?(?:[\/?#][^\s]*)?/u',

            // Local development URLs.
            '/(?:^|[\s(,;\[])localhost(?::\d{1,5})?(?:[\/?#][^\s]*)?/iu',

            // Common JavaScript injection markers without a scheme or tag.
            '/\bon(?:error|load|click|mouseover|focus)\s*=|\b(?:alert|prompt|confirm|eval)\s*\(|\bdocument\.cookie\b/iu',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $address) === 1) {
                $fail('The :attribute field must not contain website URLs, links, HTML tags, or scripts.');

                return;
            }
        }
    }
}
