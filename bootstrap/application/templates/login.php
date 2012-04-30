
<style type="text/css">
      /* Override some defaults */
      html, body {
        background-color: #eee;
      }
      body {
        padding-top: 40px;
      }
      .container {
        width: 300px;
      }
 
      /* The white background content wrapper */
      .container > .content {
        background-color: #fff;
        padding: 20px;
        margin: 0 -20px;
        -webkit-border-radius: 10px 10px 10px 10px;
           -moz-border-radius: 10px 10px 10px 10px;
                border-radius: 10px 10px 10px 10px;
        -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.15);
           -moz-box-shadow: 0 1px 2px rgba(0,0,0,.15);
                box-shadow: 0 1px 2px rgba(0,0,0,.15);
      }
 
      .login-form {
        margin-left: 65px;
      }
 
      legend {
        margin-right: -50px;
        font-weight: bold;
        color: #404040;
      }
 
    </style>    
<div class="content">
    <div class="row">
        <div class="login-form">
            <h2>Login</h2>
            <form action="<?=Url::factory()->clear_params()?>" method="POST">
                <fieldset>
                    <div class="clearfix">
                        <input name="email" type="text" placeholder="Username">
                    </div>
                    <div class="clearfix">
                        <input name="password" type="password" placeholder="Password">
                    </div>
                    <button class="btn btn-primary" type="submit">Sign in</button>
                </fieldset>
            </form>
        </div>
    </div>
</div>