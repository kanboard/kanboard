Internet Explorer ve Microsoft IIS ile oturum açamıyorum
==============================================================

Eğer giriş yapamıyorsanız ve her zaman doğru kimlik bilgilerini girmiş olsanız bile **"Kullanıcı adı veya parola gerekli" ** hatasını alırsanız,
Oturumda bir sorun olduğu anlamına gelir.

Örneğin, bu kriterler ile karşılarsanız, bu bilinen bir sorundur:

- Alt çizgi içeren bir alan adı kullanıyorsanız: `kanboard_something.mycompany.tld`
- Microsoft Windows Server ve IIS kullanıyorsanız
- Tarayıcınız Internet Explorer ise

Çözüm: **Etki alanı adında altçizgi kullanmayın; çünkü bu geçerli bir alan adı değildir**.

Açıklama: Internet Explorer geçersiz kılınmış alan adlarına sahip çerezleri kabul etmiyor çünkü bu geçerli değil.

Referans:

- https://support.microsoft.com/en-us/kb/316112
