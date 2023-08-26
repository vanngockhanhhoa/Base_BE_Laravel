<?php

namespace App\Utils;

class MessageCommon {
    /**
     * Message create success
     */
    const MS02_001 = '登録が正常に完了しました。';

    /**
     * Message update success
     */
    const MS02_002 = '編集が正常に完了しました。';

    /**
     * Message delete success
     */
    const MS02_003 = '正常に削除しました。';

    /**
     * Message create fail
     */
    const MS02_004 = 'データ登録が失敗しました。再度実行してください。';

    /**
     * Message update fail
     */
    const MS02_005 = 'データ更新が失敗しました。再度実行してください。';

    /**
     * Message delete fail
     */
    const MS02_006 = 'データ削除が失敗しました。再度実行してください。';

    /**
     * Message check exclusive/concurrent
     */
    const MS02_011 = 'データが変更されたため、ページを再読み込んでください。';

    /**
     * Message check duplicate email
     */
    const MS03_0018 = 'このメールアドレスは既に別のアカウントで使用されています。';

    /**
     * Message URL not found
     */
    const MS01_001 = 'URLが正しくありません。';

    /**
     * Condition delete department
     */
    const MS03_043 = '台帳に従業員が存在している部署を削除できません。';

    /**
     * Message is not exist customer
     */
    const MS03_043_CUSTOMER = '指定した顧客は存在しないまたは無効になっています。';

    /**
     * Message is not exist product
     */
    const MS03_043_PRODUCT = '指定した商品は存在しないまたは無効になっています。';

    /**
     * API returns unknown error
     */
    const MS01_002 = 'エラーが発生しました。再度実行してください。';

    /**
     * Error authentication 401
     */
    const MS01_003 = '認証が失敗しました。';

    /**
     * Access denied
     */
    const MS01_004 = 'アクセス権限がありません。';

    /**
     * expired token
     */
    const MS01_005 = 'セッションタイムアウトが発生しました。';

    /**
     * Message is not exist business partner
     */
    const MS03_016_BUSINESS_PARTNER = '指定した取引先は存在しないまたは無効になっています。';

    /**
     * Login fail greater than 10 times (too many request)
     */
    const MS03_068 = '10回連続で間違えて入力したため、アカウントがロックされました。{0}後に再度実行してください。';

    /**
     * Wrong URL (token expire or email wrong) when reset password
     */
    const MS03_071 = 'このURLは無効です。再度手続きをしてください。';

    /**
     * Import CSV successfully
     */
    const MS04_004 = '{n}件が正常にインポートされました。';

    const MS04_005 = 'メールアドレスまたはパスワードが正しくありません。再度入力してください。';

    /**
     * Register product laundry RC 32 34
     */
    const MS03_105 = '指定した契約は存在しないまたは無効になっています。';
    const MS03_106 = '選択された商品が該当のフリーサイズアイテムがありません。';
    const MS03_101 = '同じ商品で同じサイズが複数登録されています。';

    const MS03_114 = 'クリーニング済の商品を削除できません。';


    // CF AREA
    // CF_05 export PDF

    const MS04_007 = '出力が失敗しました。再度実行してください。';
    const MS04_008 = '出力に成功しました。';
    const MS02_009 = 'データなし';

    /**
     * Condition delete delivery
     */
    const MS03_082 = '出荷指示書で使用されている出荷先を削除できません。';
    const MS03_099 = '{0}の従業員の退職依頼は既に登録されています。';

    // RC_25_01
    const MS03_090 = 'ステータスが「未出荷」と「未入荷」の指示書以外を削除できません。';
    /**
     * Export laundry to Customer
     */
    const MS03_112 = '出荷商品一覧に要返却の商品が存在しているため出荷できません。';

    /**
     * Not exist barcode import
     */
    const MS03_116 = '入荷されていない商品を顧客へ出荷できません。';

    /**
     * Can not delete staff
     */
    const MS03_028 = 'サービスを使用した従業員を削除できません。';

	/**
     * Required content > 3000 characters
     */
    const MS03_096 = 'メッセージ内容は3000文字以内で入力してください。';    /**
     * Required check for duplicate partners
     */
    const MS03_097 = 'この取引先のメッセージチャンネルは既に存在しています。';
	/**
     * Required check extension file
     */
    const MS03_091 = '「xlsx、 xls、 docx、 doc、 pdf、 csv、 txt、 jpeg、 jpg、 png」内のファイルを添付してください。';

	/**
	 * Required check size file
     */
	const MS03_022 = '最大10MBまでのファイルを指定してください。';
    /**
     * Export with not same customer
     */
    const MS03_085 = '複数{0}の商品を一緒に出荷できません。';
    /**
     * Import barcode is washing
     */
    const MS03_124 = 'この商品は既に工場倉庫に入荷されています。';

    const MS03_019 = 'この{0}は既に登録されています。';

    /**
     * Delete barcode exported
     */
    const MS03_128 = '出荷しましたため、削除できません。';

    /**
     * Delete barcode imported
     */
    const MS03_129 = '出荷後、別の入荷がありますため、削除できません。';
    /**
     * Required check the employee registered in the ledger in the department
     */
    const MS03_127 = 'この部署が台帳で使用されているため、顧客名と店舗名を変更できません。';

    /**
     * Check The rental start date must be within the individual contract period
     */
    const MS03_051 = 'レンタル開始日は個別契約期間内にしてください。';

     /**
     * Message is not exist store
     */
    const MS03_016_STORE = '指定した店ないまたは無効になっています。';

     /**
     * Message is not exist department
     */
    const MS03_016_DEPARTMENT = '指定したデパートメントは存在しないまたは無効になっています。';

     /**
     * Message is not exist product
     */
    const MS03_016_PRODUCT = '指定した商品は存在しないまたは無効になっています。';

    /**
     * Check employees have individual leases
     */
    const MS03_084 = 'レンタルの個別契約を持っていないため従業員を追加できません。';

    /*
     * Contract is changed status to cancel or ended
     */
    const MS03_130 = '契約のステータスが「期限切れ」または「解約済み」であるため、従業員を追加できません。';

    /**
     * Message can not delete the store associated with the contract.
     */
    const MS03_131 = '契約に関連付けている店舗を削除できません。';
}


