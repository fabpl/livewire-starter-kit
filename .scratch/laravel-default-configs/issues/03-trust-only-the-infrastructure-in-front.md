# 03 — Trust only the infrastructure in front of the application

**What to build:** the application stops believing the client about who it is and where it
arrived. A request carrying a host the application does not recognise is refused, so a URL
built during a request can no longer be built from a value the client chose. A forwarded
address is honoured when the connecting peer is a proxy on a private network, and ignored
otherwise, so the client IP is a person rather than a load balancer — without becoming
something an attacker can rewrite.

The two are one ticket because splitting them would create a vulnerable commit between them.
The default set of trusted forwarded headers includes the host, so configuring proxy trust
alone reopens host injection; the trusted-hosts setting is what closes it. Neither is complete
without the other.

The vulnerability behind the host half is concrete and is lying in wait in any foundation
destined to receive authentication. Within a request, URLs are generated from the request's
host rather than from the configured application URL, so a password-reset flow driven with a
forged host produces a valid, token-bearing link pointing wherever the attacker chose. The
forced scheme of ticket 02 protects nothing here — it constrains the scheme, not the host.

No explicit host list is passed. The framework derives the value from the configured
application URL and its subdomains, which is exactly the kind of thing a foundation may decide
on behalf of its descendants, and it disables the middleware in the local environment and
under test on its own — which is why local development on a loopback address and the suite are
both unaffected without this repository doing anything.

That same self-disabling is why this is the effort's one **default taken on trust**: the suite
cannot observe it, and no level exists to move it to. The exception is written at the point of
call rather than left implicit.

The proxy half turns on an asymmetry that is not intuitive and must be preserved. Forwarded
headers are honoured only when the *immediate peer* is trusted. Configuring nothing therefore
fails safely — everyone shares one bucket, which is a nuisance, not a bypass. Trusting every
proxy fails unsafely: if the application is ever reachable directly, a forged header makes an
attacker a new address on every request. The private ranges plus loopback are decidable without
knowing the deployment, because a proxy terminating TLS sits on a private network in nearly
every topology, and a client from the public internet is never in the list. The case that is
*not* covered — an edge on public addresses, such as a CDN — belongs in the comment, because
the descendant who deploys that way should read it rather than discover it in their logs.

Do not attempt to make the proxy list configurable through the environment. The middleware
configuration callback runs when the HTTP kernel is resolved, before the environment file is
loaded, so a lookup there sees real process variables and never a value that lives only in the
environment file. A setting that works on one deployment style and silently does not on another
is worse than a literal list.

The commented session-cookie setting rides along because it addresses the same reader: the
operator deploying this foundation. It is documentation, not enforcement — a known and accepted
gap, decided as such.

**Blocked by:** None — can start immediately.

**Status:** ready-for-agent

- [ ] Trusted hosts are enabled with no explicit list, so the value derives from the configured application URL
- [ ] A comment at that call site records that the suite cannot observe it, and why
- [ ] Trusted proxies are limited to the loopback address and the three private ranges, written as literals
- [ ] A comment at that call site names the case the list does not cover — an edge on public addresses
- [ ] No environment lookup is used to build either list
- [ ] A request whose peer address is private and whose forwarded address is public resolves the client IP to the forwarded one, and a test asserts it
- [ ] A request whose peer address is public does not honour its forwarded address, and a test asserts it
- [ ] The environment example carries the session cookie's secure setting, commented, in the session block, following the file's existing convention for commented settings
- [ ] Local development over plain HTTP still works — the commented setting is not activated
- [ ] Full coverage of the application namespace still holds, with no suppression and no lowered threshold
- [ ] `composer test` passes
- [ ] Committed as a single commit following the repository's Conventional Commits convention
