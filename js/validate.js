function validate_required(field,alerttxt)
{
with (field)
  {
  if (value==null||value=="" )
    {
    alert(alerttxt);return false;
    }
  else
    {
    return true;
    }
  }
}

function validate_form(thisform)
{
with (thisform)
  {
  if (validate_required(title,"The title can't be empty")==false)
  {title.focus();return false;}
  
  if (validate_required(content,"Your content is empty >> Not acceptable")==false)
  {content.focus();return false;}
  
  var no_category=="-1";
  if (validate_required(category,"Select a category please")==no_categorys)
  {category.focus();return false;}
  }
}