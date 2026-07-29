# livewire-starter-kit

A Livewire application foundation for agent-written code, held to a mechanically enforced
quality gate. The vocabulary below exists because its distinctions are held by mechanisms
rather than by taste: each term names a category the test suite can tell apart.

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
