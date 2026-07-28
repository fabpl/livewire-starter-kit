# 02 — Force the HTTPS scheme in production

**What to build:** every URL the application generates in production uses HTTPS, whatever
scheme the incoming request reported. A form action, an asset reference or a signed link on a
page served over TLS can no longer be built over plain HTTP.

The failure this closes is mixed content, and it is quiet: the page loads, the browser may or
may not complain, and the form posts in the clear. Behind a proxy that terminates TLS the
request's own scheme is the wrong one, so the generator has no way to get it right on its own.

This does not redirect anything. It acts on generated URLs only, which is the whole of its
scope and worth stating so that it is not later mistaken for transport enforcement.

It is deliberately kept alongside the trusted-proxy configuration of ticket 03 rather than
treated as an alternative to it. Proxy trust depends on an address list somebody has to
maintain; this depends on nothing, which is what makes it the more robust of the two.

The accepted cost, recorded so that the symptom is recognisable: an application deployed with
the production environment but served over plain HTTP will generate URLs nobody can follow.
That is real, rare, and undone by commenting out one line — which the test documents.

**Blocked by:** None — can start immediately.

**Status:** ready-for-agent

- [ ] URLs generated while the application runs in production use the HTTPS scheme, and a test asserts it
- [ ] A test asserts that outside production the scheme is left as the framework produces it, following the two-case shape of the existing password-defaults test
- [ ] The test sets the application URL explicitly rather than depending on the ambient one, which the test configuration does not pin and which may be absent in the pipeline
- [ ] Full coverage of the application namespace still holds, with no suppression and no lowered threshold
- [ ] `composer test` passes
- [ ] Committed as a single commit following the repository's Conventional Commits convention
