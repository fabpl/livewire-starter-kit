# 05 — The guarantees

**What to build:** the last block of the composition — three cards stating what the gate
guarantees — and the second Primitive they are built from. This is the block that makes the
page true: it is where the repository stops describing a framework and starts describing
itself.

The claims are facts drawn from this repository's own configuration, not marketing. The
analysis level and the two rule sets that sit beyond it; the coverage minimum and the mutation
score; formatting and refactoring applied to the whole tree with no suppression mechanism
anywhere. Each of the three is checkable by opening a file in this repository, and a claim that
stops being true when a configuration changes is a claim that should fail review. If a number
is quoted, quote the one the configuration actually holds at the time of writing.

The card Primitive exposes no variants. It is a near-white surface on the parchment stage with
a warm border, a modest radius and airy padding — the layering the design document calls the
theme's signature stage. Borders do the structural work and the shadow is a whisper; the
temptation to reach for a heavier elevation is the one thing to resist here.

The three cards sit on the page as one composition, at the medium-low density the design
document prescribes, with a readable measure for their prose. Their body is reading text, so it
is the second and last place on this page that opts into the reading face.

This ticket closes the effort, so it carries the two closing obligations. The spec's status
moves to resolved. And the mutation score is re-measured, because tickets 01 to 04 changed the
measured perimeter — though in an unusual direction: this effort adds almost no mutable PHP at
all, since the Page has no body and the variant tables live outside the perimeter by design. The
score may therefore have moved without anyone deciding to move it. The pinned minimum is updated
only if the score moved **up**. If it moved down, that is a finding: report it with the escaping
mutants named, and leave the pin where it is. The bar is never lowered to make it pass.

**Parent:** [spec.md](../spec.md)

**Blocked by:** 03

**Status:** ready-for-agent

- [ ] The card Primitive exists with no variants, and passes the purity test
- [ ] Three cards state what the gate guarantees, each claim checkable against this repository's own configuration
- [ ] Any number quoted is the number the configuration holds at the time of writing
- [ ] The cards are near-white surfaces on the page stage, with border-led structure rather than heavy elevation
- [ ] Their body text opts into the reading face
- [ ] The HTTP seam asserts the three claims are present
- [ ] The purity test, the contrast test and the accessibility audit still pass
- [ ] The mutation score is re-measured and the figure recorded
- [ ] The pinned minimum is raised only if the score moved up; if it moved down, the fall is reported with the escaping mutants named and the pin is left alone
- [ ] The spec's status is moved to resolved
- [ ] `composer test` passes
- [ ] `composer browser:test` passes
- [ ] Committed as a single commit following the repository's Conventional Commits convention
