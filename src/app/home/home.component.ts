import {Component} from '@angular/core';

@Component({
    selector: 'app-home',
    templateUrl: './home.component.html',
    styleUrls: ['./home.component.scss'],
})
export class HomeComponent {
    notice: any = {
        content: '我是小黄条，小黄条的小，小黄条的黄，小黄条的条，请多多关照！！！',
        font: '14px'
    };

    state: any = {
        data: ['AiyWuByWklrrUDlFignR', 'TekJlZRVCjLFexlOCuWn', 'IJOtIlfsYdTyaDTRVrLI'],
        imgHeight: '184px'
    };

    openTab() {
        console.log('asd');
    }
}
