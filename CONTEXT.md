# livewire-starter-kit

A Livewire application foundation for agent-written code, held to a mechanically enforced
quality gate. The vocabulary below exists because its distinctions are held by mechanisms
rather than by taste: each term names a category the test suite can tell apart — including
the last one, which names what the suite cannot reach and is drawn by the same mechanism.

## Language

**Token**:
A named design value — a colour, a radius, a font family — declared once and referred to
everywhere else by name. The stylesheet is its only home.
_Avoid_: variable, custom property, theme value

**Primitive**:
An interface component that is a pure function of its props and its slots, and is therefore
portable to another project unchanged. It knows nothing of the application.
_Avoid_: atom, base component, widget, UI element

**Chrome**:
An interface component that belongs to this kit and is allowed to know the application. It
is not portable, and that is the point of the distinction from a Primitive.
_Avoid_: layout component, partial, shell

**Page**:
A full-page Livewire component reached by a route. It is the only kind of component whose
PHP the quality gate analyses in full.
_Avoid_: screen, view, route component

**Default**:
A behaviour of the framework that this foundation replaces at boot, on behalf of everything
built on it. It is decided once here so that no descendant has to decide it.
_Avoid_: setting, override, convention

**Guard**:
The condition under which a Default applies — typically the environment the application is
running in. A Default with no Guard applies everywhere.
_Avoid_: flag, toggle, condition

**Provable guard**:
A Guard whose two branches the suite can both exercise. Provability is a property of the
Guard and not of the effort spent testing it: some questions are answered the same way every
time the suite asks them, and a Guard resting on one of those can never be proved.
_Avoid_: tested guard, covered guard

**Default taken on trust**:
A Default that no Guard makes provable, adopted regardless because what it prevents outweighs
what cannot be shown. It carries its own exception in writing, and the list of them stays
short and known.
_Avoid_: untested default, assumed default

---

This file supersedes the quality gate spec's refusal of a glossary — "there is no domain here,
only tooling". The terms above are not the tooling and are not general programming vocabulary;
they name distinctions specific to this foundation, which is the test the domain-documentation
convention applies.
