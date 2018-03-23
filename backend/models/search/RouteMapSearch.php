<?php

namespace backend\models\search;

use backend\models\RouteMap;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modelsgii\Route;

/**
 * RouteSearch represents the model behind the search form about `common\models\Route`.
 */
class RouteMapSearch extends RouteMap
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['remark'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = RouteMap::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        $query -> orderBy('create_time desc');

        $query ->andFilterWhere(['like', 'remark', $this->remark]);



        return $dataProvider;
    }
}
