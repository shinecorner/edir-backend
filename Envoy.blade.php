@servers(['edir-1' => ['forge@edir-1.mensey.center'], 'edir-2' => 'forge@edir-2.mensey.center', 'edir-backend' => 'forge@edir-backend.mensey.center'])

@story('update')
    update-1
    update-2
@endstory

@story('update-all')
    update-1
    update-2
    update-backend
@endstory

@story('clear')
    clear-1
    clear-2
    clear-backend
@endstory

@task('update-1', ['on' => ['edir-1'], 'parallel' => false])
    cd ~
    cd edir-1.mensey.center
    echo $PWD
    composer update
    bash optimize.sh

    cd ~
    cd edir-3.mensey.center
    echo $PWD
    composer update
    bash optimize.sh

    cd ~
    cd edir-5.mensey.center
    echo $PWD
    composer update
    bash optimize.sh

    cd ~
    cd edir-7.mensey.center
    echo $PWD
    composer update
    bash optimize.sh

    cd ~
    cd edir-9.mensey.center
    echo $PWD
    composer update
    bash optimize.sh

    cd ~
    cd edir-11.mensey.center
    echo $PWD
    composer update
    bash optimize.sh

    cd ~
    cd edir-13.mensey.center
    echo $PWD
    composer update
    bash optimize.sh
@endtask

@task('update-2', ['on' => ['edir-2'], 'parallel' => false])
    cd ~
    cd edir-2.mensey.center
    echo $PWD
    composer update
    bash optimize.sh

    cd ~
    cd edir-4.mensey.center
    echo $PWD
    composer update
    bash optimize.sh

    cd ~
    cd edir-6.mensey.center
    echo $PWD
    composer update
    bash optimize.sh

    cd ~
    cd edir-8.mensey.center
    echo $PWD
    composer update
    bash optimize.sh

    cd ~
    cd edir-10.mensey.center
    echo $PWD
    composer update
    bash optimize.sh

    cd ~
    cd edir-12.mensey.center
    echo $PWD
    composer update
    bash optimize.sh

    cd ~
    cd edir-14.mensey.center
    echo $PWD
    composer update
    bash optimize.sh
@endtask

@task('update-backend', ['on' => ['edir-backend'], 'parallel' => false])
    cd ~
    cd edir-backend.mensey.center
    echo $PWD
    composer update
    bash optimize.sh
@endtask

@task('clear-1', ['on' => ['edir-1'], 'parallel' => false])
    cd ~
    cd edir-1.mensey.center
    echo $PWD
    php artisan cache:clear

    cd ~
    cd edir-3.mensey.center
    echo $PWD
    php artisan cache:clear

    cd ~
    cd edir-5.mensey.center
    echo $PWD
    php artisan cache:clear

    cd ~
    cd edir-7.mensey.center
    echo $PWD
    php artisan cache:clear

    cd ~
    cd edir-9.mensey.center
    echo $PWD
    php artisan cache:clear

    cd ~
    cd edir-11.mensey.center
    echo $PWD
    php artisan cache:clear

    cd ~
    cd edir-13.mensey.center
    echo $PWD
    php artisan cache:clear
@endtask

@task('clear-2', ['on' => ['edir-2'], 'parallel' => false])
    cd ~
    cd edir-2.mensey.center
    echo $PWD
    php artisan cache:clear

    cd ~
    cd edir-4.mensey.center
    echo $PWD
    php artisan cache:clear

    cd ~
    cd edir-6.mensey.center
    echo $PWD
    php artisan cache:clear

    cd ~
    cd edir-8.mensey.center
    echo $PWD
    php artisan cache:clear

    cd ~
    cd edir-10.mensey.center
    echo $PWD
    php artisan cache:clear

    cd ~
    cd edir-12.mensey.center
    echo $PWD
    php artisan cache:clear

    cd ~
    cd edir-14.mensey.center
    echo $PWD
    php artisan cache:clear
@endtask

@task('clear-backend', ['on' => ['edir-backend'], 'parallel' => false])
    cd ~
    cd edir-backend.mensey.center
    echo $PWD
    php artisan cache:clear
@endtask


@task('init-1', ['on' => ['edir-1'], 'parallel' => false])
    @if($edir && $edir % 2 == 1)
        cd ~
        cd edir-{{ $edir }}.mensey.center
        echo $PWD
        npm install
        cp ../edir-{{ $edir-1 }}.mensey.center/optimize.sh .
        bash optimize.sh
    @else
        echo "Edir-{{ $edir }} is not a valid edir. Use --edir="
    @endif
@endtask

@task('init-2', ['on' => ['edir-2'], 'parallel' => false])
    @if($edir && $edir % 2 == 0)
        cd ~
        cd edir-{{ $edir }}.mensey.center
        echo $PWD
        npm install
        cp ../edir-{{ $edir-2 }}.mensey.center/optimize.sh .
        bash optimize.sh
    @else
        echo "Edir-{{ $edir }} is not a valid edir. Use --edir="
    @endif
@endtask