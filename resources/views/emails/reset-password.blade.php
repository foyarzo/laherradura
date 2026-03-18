<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body style="margin:0; padding:0; background:#F6FBF4; font-family:Arial, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="padding:40px 0;">
<tr>
<td align="center">

<table width="600" cellpadding="0" cellspacing="0"
       style="background:#ffffff; border-radius:16px;
              border:1px solid #DDEEDD;
              box-shadow:0 20px 60px rgba(18,54,23,0.15);
              overflow:hidden;">

    {{-- Header --}}
    <tr>
        <td style="background:#0B1A10; padding:30px; text-align:center;">

        <img src="https://laherradura.dispensariolaherradura.cl/assets/img/logo_herradura.png"
             alt="La Herradura"
             width="180"
             style="display:block; margin:0 auto 20px auto;">

            <h1 style="color:#92b95d; margin:0; font-size:22px;">
                Recuperación de contraseña
            </h1>
        </td>
    </tr>

    {{-- Content --}}
    <tr>
        <td style="padding:40px; color:#123617;">

            <h2 style="margin-top:0; color:#123617;">
                Hola {{ $name }},
            </h2>

            <p style="font-size:15px; line-height:1.6;">
                Recibimos una solicitud para restablecer tu contraseña.
            </p>

            <p style="font-size:15px; line-height:1.6;">
                Haz clic en el botón a continuación para continuar:
            </p>

            <div style="text-align:center; margin:30px 0;">
                <a href="{{ $url }}"
                   style="background:#1e4e25;
                          color:#ffffff;
                          padding:14px 28px;
                          border-radius:12px;
                          text-decoration:none;
                          font-weight:bold;
                          display:inline-block;">
                    Restablecer contraseña
                </a>
            </div>

            <p style="font-size:14px; color:#3b6a33;">
                Este enlace expirará en 60 minutos.
            </p>

            <p style="font-size:14px; color:#3b6a33;">
                Si tú no solicitaste este cambio, puedes ignorar este correo.
            </p>

        </td>
    </tr>

    {{-- Footer --}}
    <tr>
        <td style="background:#F6FBF4; padding:20px; text-align:center;
                   font-size:12px; color:#3b6a33;">
            © {{ date('Y') }} La Herradura · Sistema Corporativo
        </td>
    </tr>

</table>

</td>
</tr>
</table>

</body>
</html>