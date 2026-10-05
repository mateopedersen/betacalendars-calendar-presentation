# Security policy

Please report vulnerabilities privately through GitHub's **Report a vulnerability** feature for this repository. Include the affected version, impact, and a minimal reproduction. Do not disclose an unpatched issue publicly.

The HTML renderer escapes configurable text and emits markup only. It does not load remote resources or execute scripts. Please report any case where untrusted text can escape the intended output context.
