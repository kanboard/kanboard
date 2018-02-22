Individual category markup
===========================


Setup
-----

1: Settings > Application settings > Custom Stylesheet

For the category container:
```css
.task-board-category-container-color span {
  border: solid 0.5px grey;
  color: black;
}

```
Custom css values for one category - this is an example for displaying the text: 
```css
[class*="category-Patchkanditat"] {
  background-color: rgba(255, 0, 0, 0.50);
  border: none!important;
  font-weight: bold;
  font-style: italic;
  box-shadow: 0 1px 1px rgba(186, 186, 186, 0.55);
  color: white!important;
  font-size:11px;
}
```
![CAT](../de_DE/screenshots/kanboard_patch_category.PNG)
