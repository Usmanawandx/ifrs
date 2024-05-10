import React, { useEffect } from 'react';
import {
  TouchableOpacity, Image, View, StyleSheet,FlatList
} from 'react-native';
import PropTypes from 'prop-types';
import { getScreenWidth } from 'utils/size';
import { scale, verticalScale } from 'react-native-size-matters';
import { Text } from 'components';
import axios from 'axios'
import baseUrl from '../../../../assets/common/baseUrl'

const styles = StyleSheet.create({
  container: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    flex: 1,
    paddingVertical: verticalScale(10),
  },
  button: {
    width: getScreenWidth() / 5,
    aspectRatio: 1 / 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  image: {
    // borderRadius:30,
    width: 100,
    height: 100,
    flex: 1,
  },
  seeTxt:{
    textAlign:'right',
    width:'100%',
    fontWeight:'bold',
    paddingRight:50
  }
});


const Brands = ({ navigation }) => {
  const [Brands, setBrands] = React.useState([])
  useEffect(()=>{
    axios
    .get(`${baseUrl}brands`)
    .then((res)=>{
      setBrands(res.data.Data)
      console.log("get brands")
    })
    .catch((error)=>{
      console.log("something wrong in brands")
    })
  },[])
  

return(
  <View style={styles.container}>
    {Brands.slice(0,10).map((item) => (
      <TouchableOpacity
        key={item.id}
        onPress={() => navigation.navigate('Category', { title: item.name,isfor:'brands',id:item.id })}
      >
        <View style={styles.button}>
          <Image
            source={{ uri:`https://ecpmarket.mywheels.pk/assets/images/brands/${item.photo}`}}
            resizeMode="contain"
            style={styles.image}
          />
            <Text style={{textAlign:'center',fontSize:13,height:40}} 
              color="gray75"
              numberOfLines={2}
            >
              {item.name}
            </Text>
        </View>
       
        
      </TouchableOpacity>
    ))}
    <Text onPress={()=> navigation.navigate('CategoryScreen')} style={styles.seeTxt} >See More</Text>
    
  </View>
)};

Brands.propTypes = {
  categories: PropTypes.array,
  navigation: PropTypes.object.isRequired,
};

export default Brands;
