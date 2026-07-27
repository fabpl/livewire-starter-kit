# livewire-starter-kit

## Agent skills

### Issue tracker

Issues live as markdown files under `.scratch/<feature>/` in this repo. See `docs/agents/issue-tracker.md`.

### Triage labels

The five canonical roles, each label string equal to its name. See `docs/agents/triage-labels.md`.

### Domain docs

Single-context — `CONTEXT.md` + `docs/adr/` at the repo root. See `docs/agents/domain.md`.

### Quality gate

`composer test` is the bar, and it is exactly what CI runs. Run it at the end of every implementation, before reporting anything done. Never weaken it to make it pass. See `.claude/skills/quality-gate/SKILL.md`.

### Commits

Every commit message follows Conventional Commits v1.0.0. See `.claude/skills/conventional-commits/SKILL.md`.
