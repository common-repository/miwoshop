<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-gb" lang="en-gb" dir="ltr" >
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />

    <script type="text/javascript" src="../media/jui/js/jquery.min.js" ></script>
    <script type="text/javascript" src="../media/jui/js/jquery.ui.core.js"></script>
    
    <script type="text/javascript">
        function getOption(){
            var product = document.getElementById('product');
            var option = document.getElementById('product_option');

            if( product.value == -1){
                option.innerHTML = '';
                return;
            }

            jQuery.ajax({
                url: "index.php?option=com_miwoshop&route=common/editorbutton/getProductOptions&format=raw&component=tmpl&product_id=" + product.value
            }).done(function ( data ) {
                option.innerHTML = data;
            });
        }

        function checkImage(){
            var image = document.getElementById('image');
            var div = document.getElementById('divsize');

            if(image.checked == true) {
                div.style.display = 'block';
            }
            else
            {
                div.style.display = 'none';
            }

        }

        function addToContent(){
            var product = document.getElementById('product');
            var product_options = jQuery('[name^=option_oc]');
            var price = document.getElementById('price');
            var rating = document.getElementById('rating');
            var button = document.getElementById('button_oc');
            var image = document.getElementById('image');
            var size = document.getElementById('size');
            var name = document.getElementById('name');

            if(product.value == -1) {
                return;
            }

            var _string = "miwoshop id="+product.value

            if(image.checked == true && size.value != '' ){
                _string  = _string + ',image=1:' + size.value;
            }

            if(name.checked == true ){
                _string  = _string + ',name=1';
            }

            if(price.checked == true ){
                _string  = _string + ',price=1';
            }

            if(rating.checked == true ){
                _string  = _string + ',rating=1';
            }

            if(button.checked == true ){
                _string  = _string + ',button=1';
            }

            var _opt= ',options=';
            var addoption = false;


            var i = 0, show, chb;
            for (i ; i < product_options.length; i++) {
                if(isNaN(i)){
                    break;
                }

                show = 0;
                if(product_options[i].checked){
                    show = 1;
                }

                _opt  = _opt + product_options[i].name + ':' + show +'|';
                addoption = true;
            }

            if(addoption == true) {
                _string = _string + _opt.slice(0,-1);
            }

            _string = '{' +_string + '}';

            window.parent.jInsertEditorText(_string, "<?php echo $name?>");
            window.parent.SqueezeBox.close()
        }
    </script>
    
    <style type="text/css">
       table{
           font-family: Arial,Helvetica,sans-serif;
           font-size: 12px;
           margin-left: 20px;
           margin-top: 10px;
       }
       fieldset{
           font-family: Arial,Helvetica,sans-serif;
           font-size: 14px;
           font-weight: bold;
           text-align: center;
       }

       table tr {
           height: 30px;
       }

       select{
            width: 150px;
       }

       .button {
           background: none repeat scroll 0 0 #FFFFFF;
           border: 1px solid #CCCCCC;
           margin-top: 4px;
           text-decoration: none;
       }

       button:hover {
           background: none repeat scroll 0 0 #E8F6FE;
           border: 1px solid #AAAAAA;
           cursor: pointer;
           text-decoration: none;
       }
    </style>
     
</head>
<body>
    <div id="product_button">
       <fieldset>Add Product</fieldset>
       <table >
           <tr>
               <td style="width: 150px"> Product </td>
               <td>
                   <select id="product" onchange="getOption()">
                       <option value="-1">Select</option>
                       <?php foreach($products as $product) { ?>
                           <option value="<?php echo $product['product_id'] ?>"><?php echo $product['name'] ?></option>
                       <?php } ?>
                   </select>
               </td>
           </tr>
           <tr>
               <td>Product Options</td><td><div id="product_option"></td>
           </tr>
           <tr>
               <td>Name</td>
               <td><input type="checkbox" id="name"></td>
           </tr>
           <tr>
               <td>Price</td>
               <td><input type="checkbox" id="price"></td>
           </tr>
           <tr>
               <td>Rating</td>
               <td><input type="checkbox" id="rating"></td>
           </tr>
           <tr>
               <td>Button</td>
               <td><input type="checkbox" id="button_oc"></td>
           </tr>
           <tr>
               <td>Image</td>
               <td><input type="checkbox" id="image" onchange="checkImage()"><div id="divsize" style="display: none; float: left"> Width:Height <input style="width: 50px;" type="test" id="size"></div></td>
           </tr>
           <tr>
               <td></td>
               <td><input class="button" type="button" value="Add" onclick="addToContent()"></td>
           </tr>
       </table>
    </div>
</body>
</html>